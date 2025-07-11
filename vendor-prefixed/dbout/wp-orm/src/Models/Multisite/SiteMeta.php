<?php
/**
 * Copyright © Dimitri BOUTEILLE (https://github.com/dimitriBouteille)
 * See LICENSE.txt for license details.
 *
 * Author: Dimitri BOUTEILLE <bonjour@dimitri-bouteille.fr>
 */

namespace Onepix\FoodSpotVendor\Dbout\WpOrm\Models\Multisite;

use Onepix\FoodSpotVendor\Dbout\WpOrm\Models\Meta\AbstractMeta;
use Onepix\FoodSpotVendor\Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method int getSiteId()
 * @method SiteMeta setSiteId(int $siteId)
 * @method string|null getMetaKey()
 * @method SiteMeta setMetaKey(?string $metaKey)
 * @method mixed|null getMetaValue()
 * @method SiteMeta setMetaValue($metaValue)
 *
 * @property-read Site $site
 */
class SiteMeta extends AbstractMeta
{
    final public const META_ID = 'meta_id';
    final public const SITE_ID = 'site_id';

    protected bool $useBasePrefix = true;

    protected $table = 'sitemeta';

    protected $primaryKey = self::META_ID;

    public function site(): HasOne
    {
        return $this->hasOne(Site::class, Site::ID, self::SITE_ID);
    }
}
