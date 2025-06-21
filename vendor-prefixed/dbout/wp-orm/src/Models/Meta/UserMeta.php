<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Models\Meta;

use Onepix\FoodSpotVendor\Dbout\WpOrm\Models\User;
use Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property-read User|null $user
 */
class UserMeta extends AbstractMeta
{
    final public const META_ID = 'umeta_id';
    final public const USER_ID = 'user_id';

    /**
     * @var string
     */
    protected $primaryKey = self::META_ID;

    /**
     * @var string
     */
    protected $table = 'usermeta';

    /**
     * @inheritdoc
     */
    protected bool $useBasePrefix = true;

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, User::USER_ID, self::USER_ID);
    }
}
