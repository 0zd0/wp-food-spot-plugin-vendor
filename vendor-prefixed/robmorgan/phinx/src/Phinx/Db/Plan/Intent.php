<?php
declare(strict_types=1);

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Phinx\Db\Plan;

use Onepix\FoodSpotVendor\Phinx\Db\Action\Action;

/**
 * An intent is a collection of actions for many tables
 */
class Intent
{
    /**
     * List of actions to be executed
     *
     * @var \Onepix\FoodSpotVendor\Phinx\Db\Action\Action[]
     */
    protected array $actions = [];

    /**
     * Adds a new action to the collection
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Action\Action $action The action to add
     * @return void
     */
    public function addAction(Action $action): void
    {
        $this->actions[] = $action;
    }

    /**
     * Returns the full list of actions
     *
     * @return \Onepix\FoodSpotVendor\Phinx\Db\Action\Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Merges another Intent object with this one
     *
     * @param \Onepix\FoodSpotVendor\Phinx\Db\Plan\Intent $another The other intent to merge in
     * @return void
     */
    public function merge(Intent $another): void
    {
        $this->actions = array_merge($this->actions, $another->getActions());
    }
}
