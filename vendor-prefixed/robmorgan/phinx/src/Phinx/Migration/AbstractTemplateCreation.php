<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Migration;

use Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface;
use Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractTemplateCreation implements CreationInterface
{
    /**
     * @var \Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface
     */
    protected InputInterface $input;

    /**
     * @var \Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface
     */
    protected OutputInterface $output;

    /**
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Input\InputInterface|null $input Input
     * @param \Onepix\FoodSpotVendor\Symfony\Component\Console\Output\OutputInterface|null $output Output
     */
    public function __construct(?InputInterface $input = null, ?OutputInterface $output = null)
    {
        if ($input !== null) {
            $this->setInput($input);
        }
        if ($output !== null) {
            $this->setOutput($output);
        }
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
    public function setInput(InputInterface $input): CreationInterface
    {
        $this->input = $input;

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
    public function setOutput(OutputInterface $output): CreationInterface
    {
        $this->output = $output;

        return $this;
    }
}
