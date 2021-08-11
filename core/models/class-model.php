<?php
/**
 * Singleton class for all models.
 *
 * Extend this class whenever possible to make use of common
 * methods.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    Core
 * @subpackage Model
 */

namespace DuckDev\Redirect\Models;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

use DuckDev\Redirect\Database;
use DuckDev\QueryBuilder\Query;
use DuckDev\Redirect\Utils\Base;

/**
 * Class Model
 *
 * @since   4.0.0
 * @extends Base
 * @package DuckDev\Redirect\Models\Model
 */
abstract class Model extends Base {

	/**
	 * Current model table name.
	 *
	 * @var string $table Table name.
	 * @since  4.0.0
	 * @access protected
	 */
	protected $name;

	/**
	 * Use object cache for model data.
	 *
	 * Get from cache before making complex db cals.
	 *
	 * @param string   $key      Cache key.
	 * @param callable $callback Callback.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return false|mixed
	 */
	protected function remember( $key, $callback ) {
		// Use cache.
		$log = dd4t3_cache()->remember( $key, $callback );

		return empty( $log ) ? false : $log;
	}

	/**
	 * Get the table name appending prefix.
	 *
	 * If the $core param is true, we will simply prefix the
	 * table name with current site's prefix.
	 *
	 * @param string $name Table name.
	 * @param bool   $core Is core tables.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function table_name( $name, $core = false ) {
		if ( $core ) {
			return Database\Database::instance()->get_table_name( $name );
		} else {
			$tables = Database\Installer::instance()->tables();

			return isset( $tables[ $name ] ) ? $tables[ $name ] : '';
		}
	}

	/**
	 * Get the field names of a table.
	 *
	 * @param string $table Table name.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return string[]
	 */
	protected function field_names( $table ) {
		$fields = Database\Installer::instance()->fields();

		return isset( $fields[ $table ] ) ? array_keys( $fields[ $table ] ) : array();
	}

	/**
	 * Get the field format form a table field.
	 *
	 * @param string $field Field name.
	 * @param string $table Table name.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return string
	 */
	protected function field_format( $field, $table ) {
		$fields = Database\Installer::instance()->fields();

		// If field is valid.
		if ( isset( $fields[ $table ][ $field ] ) ) {
			return $fields[ $table ][ $field ];
		}

		return '%s';
	}

	/**
	 * Prepare data to process it with DB.
	 *
	 * Allow only the fields defined in the table.
	 * Get formatting strings for each field.
	 *
	 * @param array  $data  Data.
	 * @param string $table Table name.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function prepare_data( $data, $table ) {
		$final = array(
			'values' => array(),
			'format' => array(),
		);

		// Data validation.
		foreach ( $data as $field => $value ) {
			// Only if allowed.
			if ( in_array( $field, $this->field_names( $table ), true ) ) {
				// Get format string.
				$final['format'][] = $this->field_format( $table, $field );
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
	 * @param array  $data  Data.
	 * @param string $table Table name.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return bool
	 */
	protected function insert( $data, $table ) {
		// Data validation.
		list( $data, $format ) = $this->prepare_data( $data, $table );

		// Insert data to table.
		if ( ! empty( $values ) ) {
			// phpcs:ignore
			return Query::init()
				->from( $this->table_name( $table ) )
				->insert( $data, $format );
		}

		return false;
	}

	/**
	 * Perform data update in current table.
	 *
	 * We will allow only defined table fields.
	 *
	 * @param array  $data  Data.
	 * @param array  $where Where fields.
	 * @param string $table Table name.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return bool
	 */
	protected function update( $data, $where, $table ) {
		global $wpdb;

		// Get data and formats.
		list( $data, $format ) = $this->prepare_data( $data, $table );
		// Get where formats.
		list( $where, $where_format ) = $this->prepare_data( $where, $table );

		// Update data in table.
		if ( ! empty( $values ) && ! empty( $where ) ) {
			// phpcs:ignore
			$wpdb->update(
				$this->table_name( $table ),
				$data,
				$where,
				$format,
				$where_format
			);

			return ! empty( $result );
		}

		return false;
	}

	/**
	 * Perform data update in current table.
	 *
	 * We will allow only defined table fields.
	 *
	 * @param array  $data  Data.
	 *
	 * @since  4.0.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function sanitize( array $data ) {
		if ( isset( $data['id'] ) ) {
			// ID should always be integer.
			$data['id'] = (int) $data['id'];
		}

		if ( isset( $data['url'] ) ) {
			// ID should always be integer.
			$data['url'] = substr( $url, 0, self::MAX_URL_LENGTH );
		}

		return $data;
	}
}
