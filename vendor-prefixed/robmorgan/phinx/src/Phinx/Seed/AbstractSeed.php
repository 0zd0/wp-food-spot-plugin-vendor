<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Seed;

use Onepix\FoodSpotVendor\Phinx\Db\Adapter\AdapterInterface;
use Onepix\FoodSpotVendor\Phinx\Db\Table;
use RuntimeException;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface;

/**
 * Abstract Seed Class.
 *
 * It is expected that the seeds you write extend from this class.
 *
 * This abstract class proxies the various database methods to your specified
 * adapter.
 */
abstract class AbstractSeed implements SeedInterface
{
    /**
     * @var string
     */
    protected string $environment;

    /**
     * @var \Onepix\FoodSpotVendor\Phinx\Db\Adapter\AdapterInterface
     */
    protected AdapterInterface $adapter;

    /**
     * @var \Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface
     */
    protected InputInterface $input;

    /**
     * @var \Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface
     */
    protected OutputInterface $output;

    /**
     * Override to specify dependencies for dependency injection from the configured PSR-11 container
     */
    public function __construct()
    {
    }

    /**
     * @inheritDoc
     */
    public function run(): void
    {
    }

    /**
     * @inheritDoc
     */
    public function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function setEnvironment(string $environment)
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * @inheritDoc
     */
    public function setAdapter(AdapterInterface $adapter): SeedInterface
    {
        $this->adapter = $adapter;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAdapter(): AdapterInterface
    {
        if (!isset($this->adapter)) {
            throw new RuntimeException('Cannot access `adapter` it has not been set');
        }

        return $this->adapter;
    }

    /**
     * @inheritDoc
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @inheritDoc
     */
    public function setOutput(OutputInterface $output): SeedInterface
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return static::class;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $sql, array $params = []): int
    {
        return $this->getAdapter()->execute($sql, $params);
    }

    /**
     * @inheritDoc
     */
    public function query(string $sql, array $params = []): mixed
    {
        return $this->getAdapter()->query($sql, $params);
    }

    /**
     * @inheritDoc
     */
    public function fetchRow(string $sql): array|false
    {
        return $this->getAdapter()->fetchRow($sql);
    }

    /**
     * @inheritDoc
     */
    public function fetchAll(string $sql): array
    {
        return $this->getAdapter()->fetchAll($sql);
    }

    /**
     * @inheritDoc
     */
    public function insert(string $table, array $data): void
    {
        // convert to table object
        if (is_string($table)) {
            $table = new Table($table, [], $this->getAdapter());
        }
        $table->insert($data)->save();
    }

    /**
     * @inheritDoc
     */
    public function hasTable(string $tableName): bool
    {
        return $this->getAdapter()->hasTable($tableName);
    }

    /**
     * @inheritDoc
     */
    public function table(string $tableName, array $options = []): Table
    {
        return new Table($tableName, $options, $this->getAdapter());
    }

    /**
     * Checks to see if the seed should be executed.
     *
     * Returns true by default.
     *
     * You can use this to prevent a seed from executing.
     *
     * @return bool
     */
    public function shouldExecute(): bool
    {
        return true;
    }
}
