<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Console\Command;

use InvalidArgumentException;
use Onepix\FoodSpotVendor\Phinx\Migration\Manager\Environment;
use Onepix\FoodSpotVendor\Phinx\Util\Util;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Attribute\AsCommand;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputOption;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'test')]
class Test extends AbstractCommand
{
    /**
     * @var string|null
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $defaultName = 'test';

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->addOption('--environment', '-e', InputOption::VALUE_REQUIRED, 'The target environment');

        $this->setDescription('Verify the configuration file')
            ->setHelp(
                <<<EOT
The <info>test</info> command is used to verify the phinx configuration file and optionally an environment

<info>phinx test</info>
<info>phinx test -e development</info>

If the environment option is set, it will test that phinx can connect to the DB associated with that environment
EOT,
            );
    }

    /**
     * Verify configuration file
     *
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface $input Input
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface $output Output
     * @throws \InvalidArgumentException
     * @return int 0 on success
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->hasConfig()) {
            $this->loadConfig($input, $output);
        }

        $this->loadManager($input, $output);

        // Verify the migrations path(s)
        array_map(
            [$this, 'verifyMigrationDirectory'],
            Util::globAll($this->getConfig()->getMigrationPaths()),
        );

        // Verify the seed path(s)
        array_map(
            [$this, 'verifySeedDirectory'],
            Util::globAll($this->getConfig()->getSeedPaths()),
        );

        $envName = $input->getOption('environment');
        if ($envName) {
            if (!$this->getConfig()->hasEnvironment($envName)) {
                throw new InvalidArgumentException(sprintf(
                    'The environment "%s" does not exist',
                    $envName,
                ));
            }

            $output->writeln(sprintf('<info>validating environment</info> %s', $envName), $this->verbosityLevel);
            $environment = new Environment(
                $envName,
                $this->getConfig()->getEnvironment($envName),
            );
            // validate environment connection
            $environment->getAdapter()->connect();
        }

        $output->writeln('<info>success!</info>', $this->verbosityLevel);

        return self::CODE_SUCCESS;
    }
}
