<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Console\Command;

use Onepix\FoodSpotVendor\Phinx\Util\Util;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Attribute\AsCommand;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'list:aliases')]
class ListAliases extends AbstractCommand
{
    /**
     * @var string|null
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $defaultName = 'list:aliases';

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setDescription('List template class aliases')
            ->setHelp('The <info>list:aliases</info> command lists the migration template generation class aliases');
    }

    /**
     * List migration template creation aliases.
     *
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface $input Input
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface $output Output
     * @return int 0 on success
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bootstrap($input, $output);

        $aliases = $this->config->getAliases();

        if ($aliases) {
            $maxAliasLength = max(array_map('strlen', array_keys($aliases)));
            $maxClassLength = max(array_map('strlen', $aliases));
            $output->writeln(
                array_merge(
                    [
                        '',
                        sprintf('%s %s', str_pad('Alias', $maxAliasLength), str_pad('Class', $maxClassLength)),
                        sprintf('%s %s', str_repeat('=', $maxAliasLength), str_repeat('=', $maxClassLength)),
                    ],
                    array_map(
                        function ($alias, $class) use ($maxAliasLength, $maxClassLength) {
                            return sprintf('%s %s', str_pad($alias, $maxAliasLength), str_pad($class, $maxClassLength));
                        },
                        array_keys($aliases),
                        $aliases,
                    ),
                ),
                $this->verbosityLevel,
            );
        } else {
            $output->writeln(
                '<comment>warning</comment> no aliases defined in ' . Util::relativePath(
                    $this->config->getConfigFilePath(),
                ),
                $this->verbosityLevel,
            );
        }

        return self::CODE_SUCCESS;
    }
}
