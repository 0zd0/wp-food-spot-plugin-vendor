<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Migration;

use Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface;

/**
 * Migration interface
 */
interface CreationInterface
{
    /**
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface|null $input Input
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface|null $output Output
     */
    public function __construct(?InputInterface $input = null, ?OutputInterface $output = null);

    /**
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface $input Input
     * @return $this
     */
    public function setInput(InputInterface $input);

    /**
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface $output Output
     * @return $this
     */
    public function setOutput(OutputInterface $output);

    /**
     * @return \Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface
     */
    public function getInput(): InputInterface;

    /**
     * @return \Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput(): OutputInterface;

    /**
     * Get the migration template.
     *
     * This will be the content that Phinx will amend to generate the migration file.
     *
     * @return string The content of the template for Phinx to amend.
     */
    public function getMigrationTemplate(): string;

    /**
     * Post Migration Creation.
     *
     * Once the migration file has been created, this method will be called, allowing any additional
     * processing, specific to the template to be performed.
     *
     * @param string $migrationFilename The name of the newly created migration.
     * @param string $className The class name.
     * @param string $baseClassName The name of the base class.
     * @return void
     */
    public function postMigrationCreation(string $migrationFilename, string $className, string $baseClassName): void;
}
