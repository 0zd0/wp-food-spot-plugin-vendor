<?php

/**
 * @see https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata#PhpStormAdvancedMetadata-Factorymethods
 */

namespace Onepix\FoodSpotVendor\PHPSTORM_META {

	// Carbon_Fields::resolve()
	override(
		\Onepix\FoodSpotVendor\Carbon_Fields\Carbon_Fields::resolve( 0 ),
		map(
			[
				'container_condition_fulfillable_collection' => \Onepix\FoodSpotVendor\Carbon_Fields\Container\Fulfillable\Fulfillable_Collection::class,
				'container_condition_translator_json' => \Onepix\FoodSpotVendor\Carbon_Fields\Container\Fulfillable\Translator\Json_Translator::class,
				'container_repository' => \Onepix\FoodSpotVendor\Carbon_Fields\Container\Repository::class,
				'containers' => \Onepix\FoodSpotVendor\Carbon_Fields\Pimple\Container::class,
				'fields' => \Onepix\FoodSpotVendor\Carbon_Fields\Pimple\Container::class,
				'key_toolset' => \Onepix\FoodSpotVendor\Carbon_Fields\Toolset\Key_Toolset::class,
				'loader' => \Onepix\FoodSpotVendor\Carbon_Fields\Loader\Loader::class,
				'rest_api_decorator' => \Onepix\FoodSpotVendor\Carbon_Fields\REST_API\Decorator::class,
				'rest_api_router' => \Onepix\FoodSpotVendor\Carbon_Fields\REST_API\Router::class,
				'sidebar_manager' => \Onepix\FoodSpotVendor\Carbon_Fields\Libraries\Sidebar_Manager\Sidebar_Manager::class,
				'wp_toolset' => \Onepix\FoodSpotVendor\Carbon_Fields\Toolset\WP_Toolset::class,
				/* Events */
				'event_emitter' => \Onepix\FoodSpotVendor\Carbon_Fields\Event\Emitter::class,
				'event_persistent_listener' => \Onepix\FoodSpotVendor\Carbon_Fields\Event\PersistentListener::class,
				'event_single_event_listener' => \Onepix\FoodSpotVendor\Carbon_Fields\Event\SingleEventListener::class,
			]
		)
	);

	// Carbon_Fields::service()
	override(
		\Onepix\FoodSpotVendor\Carbon_Fields\Carbon_Fields::service( 0 ),
		map(
			[
				/* Services */
				'legacy_storage' => \Onepix\FoodSpotVendor\Carbon_Fields\Service\Legacy_Storage_Service_v_1_5::class,
				'meta_query' => \Onepix\FoodSpotVendor\Carbon_Fields\Service\Meta_Query_Service::class,
				'rest_api' => \Onepix\FoodSpotVendor\Carbon_Fields\Service\REST_API_Service::class,
				'revisions' => \Onepix\FoodSpotVendor\Carbon_Fields\Service\Revisions_Service::class,
			]
		)
	);


	// Pimple
	override(
		new \Onepix\FoodSpotVendor\Carbon_Fields\Pimple\Container,
		map(
			[
				'container_conditions' => \Onepix\FoodSpotVendor\Carbon_Fields\Pimple\Container::class,
			]
		)
	);

}
