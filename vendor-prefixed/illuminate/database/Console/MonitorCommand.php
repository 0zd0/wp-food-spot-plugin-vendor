<?php

namespace Onepix\FoodSpotVendor\Illuminate\Database\Console;

use Onepix\FoodSpotVendor\Illuminate\Contracts\Events\Dispatcher;
use Onepix\FoodSpotVendor\Illuminate\Database\ConnectionResolverInterface;
use Onepix\FoodSpotVendor\Illuminate\Database\Events\DatabaseBusy;
use Onepix\FoodSpotVendor\Illuminate\Support\Collection;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'db:monitor')]
class MonitorCommand extends DatabaseInspectionCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:monitor
                {--databases= : The database connections to monitor}
                {--max= : The maximum number of connections that can be open before an event is dispatched}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor the number of connections on the specified database';

    /**
     * The connection resolver instance.
     *
     * @var \Onepix\FoodSpotVendor\Illuminate\Database\ConnectionResolverInterface
     */
    protected $connection;

    /**
     * The events dispatcher instance.
     *
     * @var \Onepix\FoodSpotVendor\Illuminate\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * Create a new command instance.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Database\ConnectionResolverInterface  $connection
     * @param  \Onepix\FoodSpotVendor\Illuminate\Contracts\Events\Dispatcher  $events
     */
    public function __construct(ConnectionResolverInterface $connection, Dispatcher $events)
    {
        parent::__construct();

        $this->connection = $connection;
        $this->events = $events;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $databases = $this->parseDatabases($this->option('databases'));

        $this->displayConnections($databases);

        if ($this->option('max')) {
            $this->dispatchEvents($databases);
        }
    }

    /**
     * Parse the database into an array of the connections.
     *
     * @param  string  $databases
     * @return \Onepix\FoodSpotVendor\Illuminate\Support\Collection
     */
    protected function parseDatabases($databases)
    {
        return (new Collection(explode(',', $databases)))->map(function ($database) {
            if (! $database) {
                $database = $this->laravel['config']['database.default'];
            }

            $maxConnections = $this->option('max');

            $connections = $this->connection->connection($database)->threadCount();

            return [
                'database' => $database,
                'connections' => $connections,
                'status' => $maxConnections && $connections >= $maxConnections ? '<fg=yellow;options=bold>ALERT</>' : '<fg=green;options=bold>OK</>',
            ];
        });
    }

    /**
     * Display the databases and their connection counts in the console.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Support\Collection  $databases
     * @return void
     */
    protected function displayConnections($databases)
    {
        $this->newLine();

        $this->components->twoColumnDetail('<fg=gray>Database name</>', '<fg=gray>Connections</>');

        $databases->each(function ($database) {
            $status = '['.$database['connections'].'] '.$database['status'];

            $this->components->twoColumnDetail($database['database'], $status);
        });

        $this->newLine();
    }

    /**
     * Dispatch the database monitoring events.
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Support\Collection  $databases
     * @return void
     */
    protected function dispatchEvents($databases)
    {
        $databases->each(function ($database) {
            if ($database['status'] === '<fg=green;options=bold>OK</>') {
                return;
            }

            $this->events->dispatch(
                new DatabaseBusy(
                    $database['database'],
                    $database['connections']
                )
            );
        });
    }
}
