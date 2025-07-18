<?php

namespace Onepix\FoodSpotVendor\Carbon_Fields\Datastore;

use Onepix\FoodSpotVendor\Carbon_Fields\Field\Field;

/**
 * Empty datastore class.
 */
class Empty_Datastore extends Datastore {
	/**
	 * {@inheritDoc}
	 */
	public function init() {}

	/**
	 * {@inheritDoc}
	 */
	public function load( Field $field ) {}

	/**
	 * {@inheritDoc}
	 */
	public function save( Field $field ) {}

	/**
	 * {@inheritDoc}
	 */
	public function delete( Field $field ) {}
}
