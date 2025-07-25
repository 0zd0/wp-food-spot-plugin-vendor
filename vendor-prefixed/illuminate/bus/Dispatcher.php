<?php

namespace Onepix\FoodSpotVendor\Illuminate\Bus;

use Closure;
use Onepix\FoodSpotVendor\Illuminate\Contracts\Bus\QueueingDispatcher;
use Onepix\FoodSpotVendor\Illuminate\Contracts\Container\Container;
use Onepix\FoodSpotVendor\Illuminate\Contracts\Queue\Queue;
use Onepix\FoodSpotVendor\Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\PendingChain;
use Onepix\FoodSpotVendor\Illuminate\Pipeline\Pipeline;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\SyncJob;
use Onepix\FoodSpotVendor\Illuminate\Support\Collection;
use RuntimeException;

class Dispatcher implements QueueingDispatcher
{
    /**
     * The container implementation.
     *
     * @var \Onepix\FoodSpotVendor\Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * The pipeline instance for the bus.
     *
     * @var \Onepix\FoodSpotVendor\Illuminate\Pipeline\Pipeline
     */
    protected $pipeline;

    /**
     * The pipes to send commands through before dispatching.
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * The command to handler mapping for non-self-handling events.
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * The queue resolver callback.
     *
     * @var \Closure|null
     */
    protected $queueResolver;

    /**
     * Create a new command dispatcher instance.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Contracts\Container\Container  $container
     * @param  \Closure|null  $queueResolver
     * @return void
     */
    public function __construct(Container $container, ?Closure $queueResolver = null)
    {
        $this->container = $container;
        $this->queueResolver = $queueResolver;
        $this->pipeline = new Pipeline($container);
    }

    /**
     * Dispatch a command to its appropriate handler.
     *
     * @param  mixed  $command
     * @return mixed
     */
    public function dispatch($command)
    {
        return $this->queueResolver && $this->commandShouldBeQueued($command)
                        ? $this->dispatchToQueue($command)
                        : $this->dispatchNow($command);
    }

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * Queueable jobs will be dispatched to the "sync" queue.
     *
     * @param  mixed  $command
     * @param  mixed  $handler
     * @return mixed
     */
    public function dispatchSync($command, $handler = null)
    {
        if ($this->queueResolver &&
            $this->commandShouldBeQueued($command) &&
            method_exists($command, 'onConnection')) {
            return $this->dispatchToQueue($command->onConnection('sync'));
        }

        return $this->dispatchNow($command, $handler);
    }

    /**
     * Dispatch a command to its appropriate handler in the current process without using the synchronous queue.
     *
     * @param  mixed  $command
     * @param  mixed  $handler
     * @return mixed
     */
    public function dispatchNow($command, $handler = null)
    {
        $uses = onepix_foodspotvendor_class_uses_recursive($command);

        if (isset($uses[InteractsWithQueue::class], $uses[Queueable::class]) && ! $command->job) {
            $command->setJob(new SyncJob($this->container, json_encode([]), 'sync', 'sync'));
        }

        if ($handler || $handler = $this->getCommandHandler($command)) {
            $callback = function ($command) use ($handler) {
                $method = method_exists($handler, 'handle') ? 'handle' : '__invoke';

                return $handler->{$method}($command);
            };
        } else {
            $callback = function ($command) {
                $method = method_exists($command, 'handle') ? 'handle' : '__invoke';

                return $this->container->call([$command, $method]);
            };
        }

        return $this->pipeline->send($command)->through($this->pipes)->then($callback);
    }

    /**
     * Attempt to find the batch with the given ID.
     *
     * @param  string  $batchId
     * @return \Onepix\FoodSpotVendor\Illuminate\Bus\Batch|null
     */
    public function findBatch(string $batchId)
    {
        return $this->container->make(BatchRepository::class)->find($batchId);
    }

    /**
     * Create a new batch of queueable jobs.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Support\Collection|array|mixed  $jobs
     * @return \Onepix\FoodSpotVendor\Illuminate\Bus\PendingBatch
     */
    public function batch($jobs)
    {
        return new PendingBatch($this->container, Collection::wrap($jobs));
    }

    /**
     * Create a new chain of queueable jobs.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Support\Collection|array  $jobs
     * @return \Illuminate\Foundation\Bus\PendingChain
     */
    public function chain($jobs)
    {
        $jobs = Collection::wrap($jobs);
        $jobs = ChainedBatch::prepareNestedBatches($jobs);

        return new PendingChain($jobs->shift(), $jobs->toArray());
    }

    /**
     * Determine if the given command has a handler.
     *
     * @param  mixed  $command
     * @return bool
     */
    public function hasCommandHandler($command)
    {
        return array_key_exists(get_class($command), $this->handlers);
    }

    /**
     * Retrieve the handler for a command.
     *
     * @param  mixed  $command
     * @return bool|mixed
     */
    public function getCommandHandler($command)
    {
        if ($this->hasCommandHandler($command)) {
            return $this->container->make($this->handlers[get_class($command)]);
        }

        return false;
    }

    /**
     * Determine if the given command should be queued.
     *
     * @param  mixed  $command
     * @return bool
     */
    protected function commandShouldBeQueued($command)
    {
        return $command instanceof ShouldQueue;
    }

    /**
     * Dispatch a command to its appropriate handler behind a queue.
     *
     * @param  mixed  $command
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function dispatchToQueue($command)
    {
        $connection = $command->connection ?? null;

        $queue = ($this->queueResolver)($connection);

        if (! $queue instanceof Queue) {
            throw new RuntimeException('Queue resolver did not return a Queue implementation.');
        }

        if (method_exists($command, 'queue')) {
            return $command->queue($queue, $command);
        }

        return $this->pushCommandToQueue($queue, $command);
    }

    /**
     * Push the command onto the given queue instance.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Contracts\Queue\Queue  $queue
     * @param  mixed  $command
     * @return mixed
     */
    protected function pushCommandToQueue($queue, $command)
    {
        if (isset($command->delay)) {
            return $queue->later($command->delay, $command, queue: $command->queue ?? null);
        }

        return $queue->push($command, queue: $command->queue ?? null);
    }

    /**
     * Dispatch a command to its appropriate handler after the current process.
     *
     * @param  mixed  $command
     * @param  mixed  $handler
     * @return void
     */
    public function dispatchAfterResponse($command, $handler = null)
    {
        $this->container->terminating(function () use ($command, $handler) {
            $this->dispatchSync($command, $handler);
        });
    }

    /**
     * Set the pipes through which commands should be piped before dispatching.
     *
     * @param  array  $pipes
     * @return $this
     */
    public function pipeThrough(array $pipes)
    {
        $this->pipes = $pipes;

        return $this;
    }

    /**
     * Map a command to a handler.
     *
     * @param  array  $map
     * @return $this
     */
    public function map(array $map)
    {
        $this->handlers = array_merge($this->handlers, $map);

        return $this;
    }
}
