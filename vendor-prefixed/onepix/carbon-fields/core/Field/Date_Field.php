<?php

namespace Onepix\FoodSpotVendor\Carbon_Fields\Field;

/**
 * Date picker field class.
 */
class Date_Field extends Field {

	/**
	 * {@inheritDoc}
	 */
	protected $allowed_attributes = array( 'placeholder', 'autocomplete' );

	/**
	 * The storage format for use in PHP
	 *
	 * @var string
	 */
	protected $storage_format = 'Y-m-d';

	/**
	 * The expected input format for use in PHP
	 *
	 * @var string
	 */
	protected $input_format_php = 'Y-m-d';

	/**
	 * The expected input format for use in Flatpickr JS
	 *
	 * @var string
	 */
	protected $input_format_js = 'Y-m-d';

	/**
	 * Picker options.
	 *
	 * @var array
	 */
	protected $picker_options = array(
		'allowInput' => true,
		'altInput' => true,
		'altFormat' => "j M Y",
	);

	/**
	 * {@inheritDoc}
	 */
	public function set_value_from_input( $input ) {
		if ( isset( $input[ $this->get_name() ] ) ) {
			$date = \DateTime::createFromFormat( $this->input_format_php, $input[ $this->get_name() ] );
			$value = ( $date instanceof \DateTime ) ? $date->format( $this->storage_format ) : '';
			$this->set_value( $value );
		} else {
			$this->clear_value();
		}
		return $this;
	}

	/**
	 * {@inheritDoc}
	 */
	public function to_json( $load ) {
		$field_data = parent::to_json( $load );

		$value = $this->get_value();
		if ( ! empty( $value ) ) {
			$date = \DateTime::createFromFormat( $this->storage_format, $value );
			$value = ( $date instanceof \DateTime ) ? $date->format( $this->input_format_php ) : '';
		}

		$field_data = array_merge( $field_data, array(
			'value' => $value,
			'storage_format' => $this->get_storage_format(),
			'picker_options' => array_merge( $this->get_picker_options(), array(
				'dateFormat' => $this->input_format_js,
			) ),
		) );

		return $field_data;
	}

	/**
	 * Get storage format
	 *
	 * @return string
	 */
	public function get_storage_format() {
		if ( $this->get_context() === 'block' ) {
			$this->input_format_js = "Y-m-d h:i:S K";

			return "Y-m-d H:i:s";
		}

		return $this->storage_format;
	}

	/**
	 * Set storage format
	 *
	 * @param  string $storage_format
	 * @return self   $this
	 */
	public function set_storage_format( $storage_format ) {
		$this->storage_format = $storage_format;
		return $this;
	}

	/**
	 * Get the expected input format in php and js variants
	 *
	 * @param string $php_format
	 * @param string $js_format
	 * @return self $this
	 */
	public function get_input_format( $php_format, $js_format ) {
		$this->input_format_php = $php_format;
		$this->input_format_js = $js_format;
		return $this;
	}

	/**
	 * Set a format for use on the front-end in both PHP and Flatpickr formats
	 * The formats should produce identical results (i.e. they are translations of each other)
	 *
	 * @param  string $php_format
	 * @param  string $js_format
	 * @return self   $this
	 */
	public function set_input_format( $php_format, $js_format ) {
		$this->input_format_php = $php_format;
		$this->input_format_js = $js_format;
		return $this;
	}

	/**
	 * Returns the picker options.
	 *
	 * @return array
	 */
	public function get_picker_options() {
		return $this->picker_options;
	}

	/**
	 * Set datepicker options
	 *
	 * @param  array $options
	 * @return self  $this
	 */
	public function set_picker_options( $options ) {
		$this->picker_options = array_replace( $this->picker_options, $options );
		return $this;
	}
}
