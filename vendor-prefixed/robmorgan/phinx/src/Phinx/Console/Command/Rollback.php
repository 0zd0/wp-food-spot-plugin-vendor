<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Console\Command;

use DateTime;
use InvalidArgumentException;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Attribute\AsCommand;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputOption;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'rollback')]
class Rollback extends AbstractCommand
{
    /**
     * @var string|null
     */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
    protected static $defaultName = 'rollback';

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->addOption('--environment', '-e', InputOption::VALUE_REQUIRED, 'The target environment');

        $this->setDescription('Rollback the last or to a specific migration')
            ->addOption('--target', '-t', InputOption::VALUE_REQUIRED, 'The version number to rollback to')
            ->addOption('--date', '-d', InputOption::VALUE_REQUIRED, 'The date to rollback to')
            ->addOption('--force', '-f', InputOption::VALUE_NONE, 'Force rollback to ignore breakpoints')
            ->addOption('--dry-run', '-x', InputOption::VALUE_NONE, 'Dump query to standard output instead of executing it')
            ->addOption('--fake', null, InputOption::VALUE_NONE, "Mark any rollbacks selected as run, but don't actually execute them")
            ->setHelp(
                <<<EOT
The <info>rollback</info> command reverts the last migration, or optionally up to a specific version

<info>phinx rollback -e development</info>
<info>phinx rollback -e development -t 20111018185412</info>
<info>phinx rollback -e development -d 20111018</info>
<info>phinx rollback -e development -v</info>
<info>phinx rollback -e development -t 20111018185412 -f</info>

If you have a breakpoint set, then you can rollback to target 0 and the rollbacks will stop at the breakpoint.
<info>phinx rollback -e development -t 0 </info>

The <info>version_order</info> configuration option is used to determine the order of the migrations when rolling back.
This can be used to allow the rolling back of the last executed migration instead of the last created one, or combined
with the <info>-d|--date</info> option to rollback to a certain date using the migration start times to order them.

EOT,
            );
    }

    /**
     * Rollback the migration.
     *
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface $input Input
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface $output Output
     * @return int integer 0 on success, or an error code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bootstrap($input, $output);

        $environment = $input->getOption('environment');
        $version = $input->getOption('target');
        $date = $input->getOption('date');
        $force = (bool)$input->getOption('force');
        $fake = (bool)$input->getOption('fake');

        $success = $this->writeInformationOutput($environment, $output);
        if (!$success) {
            return self::CODE_ERROR;
        }

        $versionOrder = $this->getConfig()->getVersionOrder();
        $output->writeln('<info>ordering by</info> ' . $versionOrder . ' time', $this->verbosityLevel);

        if ($fake) {
            $output->writeln('<comment>warning</comment> performing fake rollbacks', $this->verbosityLevel);
        }

        // rollback the specified environment
        if ($date === null) {
            $targetMustMatchVersion = true;
            $target = $version;
        } else {
            $targetMustMatchVersion = false;
            $target = $this->getTargetFromDate($date);
        }

        $start = microtime(true);
        $this->getManager()->rollback($environment, $target, $force, $targetMustMatchVersion, $fake);
        $end = microtime(true);

        $output->writeln('', $this->verbosityLevel);
        $output->writeln('<comment>All Done. Took ' . sprintf('%.4fs', $end - $start) . '</comment>', $this->verbosityLevel);

        return self::CODE_SUCCESS;
    }

    /**
     * Get Target from Date
     *
     * @param string $date The date to convert to a target.
     * @throws \InvalidArgumentException
     * @return string The target
     */
    public function getTargetFromDate(string $date): string
    {
        if (!preg_match('/^\d{4,14}$/', $date)) {
            throw new InvalidArgumentException('Invalid date. Format is YYYY[MM[DD[HH[II[SS]]]]].');
        }

        // what we need to append to the date according to the possible date string lengths
        $dateStrlenToAppend = [
            14 => '',
            12 => '00',
            10 => '0000',
            8 => '000000',
            6 => '01000000',
            4 => '0101000000',
        ];

        if (!isset($dateStrlenToAppend[strlen($date)])) {
            throw new InvalidArgumentException('Invalid date. Format is YYYY[MM[DD[HH[II[SS]]]]].');
        }

        $target = $date . $dateStrlenToAppend[strlen($date)];

        $dateTime = DateTime::createFromFormat('YmdHis', $target);

        if ($dateTime === false) {
            throw new InvalidArgumentException('Invalid date. Format is YYYY[MM[DD[HH[II[SS]]]]].');
        }

        return $dateTime->format('YmdHis');
    }
}
