<?php
/**
 * Singleton class for all models.
 *
 * Extend this class whenever possible to make use of common
 * methods.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    40to301
 * @since      4.0.0
 * @subpackage Model
 */

namespace DuckDev\Redirect\Utils\Abstracts;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Base
 *
 * @package DuckDev\DD4T3\Abstracts
 */
abstract class Model extends Base {

	/**
	 * Current model table name.
	 *
	 * @var string $table Table name.
	 *
	 * @since 4.0
	 */
	protected $table;

	/**
	 * Field names of the table.
	 *
	 * @var string[] $fields
	 *
	 * @since 4.0.0
	 */
	protected $fields = array();

	/**
	 * Get the table name appending prefix.
	 *
	 * Classes can override this by extending it.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	protected function table_name() {
		global $wpdb;

		return $wpdb->prefix . $this->table;
	}

	/**
	 * Get the field names of the table.
	 *
	 * @since 4.0
	 *
	 * @return string[]
	 */
	protected function field_names() {
		return array_keys( $this->fields );
	}

	/**
	 * Get the field format string.
	 *
	 * @param string $name Field name.
	 *
	 * @since 4.0
	 *
	 * @return string
	 */
	protected function field_format( $name ) {
		return isset( $this->fields[ $name ] ) ? $this->fields[ $name ] : '%s';
	}

	/**
	 * Prepare data to process it with DB.
	 *
	 * Allow only the fields defined in the table.
	 * Get formatting strings for each fields.
	 *
	 * @param array $data Data.
	 *
	 * @since 4.0.0
	 *
	 * @return array
	 */
	protected function prepare_data( $data ) {
		$final = array(
			'values' => array(),
			'format' => array(),
		);

		// Data validation.
		foreach ( $data as $field => $value ) {
			// Only if allowed.
			if ( in_array( $field, $this->field_names(), true ) ) {
				// Get format string.
				$final['format'][] = $this->field_format( $field );
				// Get value.
				$final['values'][ $field ] = $value;
			}
		}

		return $final;
	}

	/**
	 * Perform data insert to current table.
	 *
	 * We will allow only fields added in $fields var.
	 *
	 * @param array $data Data.
	 *
	 * @since 4.0
	 *
	 * @return bool
	 */
	protected function insert( $data ) {
		global $wpdb;

		// Data validation.
		list( $data, $format ) = $this->prepare_data( $data );

		// Insert data to table.
		if ( ! empty( $values ) ) {
			// phpcs:ignore
			$result = $wpdb->insert( $this->table_name(), $data, $format );

			return ! empty( $result );
		}

		return false;
	}

	/**
	 * Perform data update in current table.
	 *
	 * We will allow only fields added in $fields var.
	 *
	 * @param array $data  Data.
	 * @param array $where Where fields.
	 *
	 * @since 4.0
	 *
	 * @return bool
	 */
	protected function update( $data, $where ) {
		global $wpdb;

		// Get data and formats.
		list( $data, $format ) = $this->prepare_data( $data );
		// Get where formats.
		list( $where, $where_format ) = $this->prepare_data( $where );

		// Update data in table.
		if ( ! empty( $values ) && ! empty( $where ) ) {
			// phpcs:ignore
			$wpdb->update( $this->table_name(), $data, $where, $format, $where_format );

			return ! empty( $result );
		}

		return false;
	}
}
