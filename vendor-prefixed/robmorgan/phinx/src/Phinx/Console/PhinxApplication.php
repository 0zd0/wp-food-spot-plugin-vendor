<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Console;

use Composer\InstalledVersions;
use Onepix\FoodSpotVendor\Phinx\Console\Command\Breakpoint;
use Onepix\FoodSpotVendor\Phinx\Console\Command\Create;
use Onepix\FoodSpotVendor\Phinx\Console\Command\Init;
use Onepix\FoodSpotVendor\Phinx\Console\Command\ListAliases;
use Onepix\FoodSpotVendor\Phinx\Console\Command\Migrate;
use Onepix\FoodSpotVendor\Phinx\Console\Command\Rollback;
use Onepix\FoodSpotVendor\Phinx\Console\Command\SeedCreate;
use Onepix\FoodSpotVendor\Phinx\Console\Command\SeedRun;
use Onepix\FoodSpotVendor\Phinx\Console\Command\Status;
use Onepix\FoodSpotVendor\Phinx\Console\Command\Test;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Application;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface;

/**
 * Phinx console application.
 */
class PhinxApplication extends Application
{
    /**
     * @var string The current application version as determined by the getVersion() function.
     */
    private string $version;

    /**
     * Initialize the Phinx console application.
     */
    public function __construct()
    {
        parent::__construct('Phinx by CakePHP - https://phinx.org.', $this->getVersion());

        $this->addCommands([
            new Init(),
            new Create(),
            new Migrate(),
            new Rollback(),
            new Status(),
            new Breakpoint(),
            new Test(),
            new SeedCreate(),
            new SeedRun(),
            new ListAliases(),
        ]);
    }

    /**
     * Runs the current application.
     *
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface $input An Input instance
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface $output An Output instance
     * @return int 0 if everything went fine, or an error code
     */
    public function doRun(InputInterface $input, OutputInterface $output): int
    {
        // always show the version information except when the user invokes the help
        // command as that already does it
        if ($input->hasParameterOption('--no-info') === false) {
            if (($input->hasParameterOption(['--help', '-h']) !== false) || ($input->getFirstArgument() !== null && $input->getFirstArgument() !== 'list')) {
                $output->writeln($this->getLongVersion());
                $output->writeln('');
            }
        }

        return parent::doRun($input, $output);
    }

    /**
     * Get the current application version.
     *
     * @return string The application version if it could be found, otherwise 'UNKNOWN'
     */
    public function getVersion(): string
    {
        if (isset($this->version)) {
            return $this->version;
        }

        // humbug/box will replace this with actual version when building
        // so use that if available
        $gitTag = '@git_tag@';
        if (!str_starts_with($gitTag, '@')) {
            return $this->version = $gitTag;
        }

        // Otherwise fallback to the version as reported by composer
        return $this->version = InstalledVersions::getPrettyVersion('robmorgan/phinx') ?? 'UNKNOWN';
    }
}
