<?php
/**
 * DataViews → BerlinDB filter translator.
 *
 * The Logs and Redirects list endpoints accept a structured
 * `filters` array that mirrors `@wordpress/dataviews` v16's
 * `view.filters` shape: `[{ field, operator, value }, …]`.
 *
 * This class validates each entry against a per-field operator
 * allowlist and translates it into BerlinDB query args
 * ({@see \BerlinDB\Database\Query}). Allowlists are passed in by the
 * caller so the same mapper drives both endpoints.
 *
 * @package DuckDev\FourNotFour
 */

declare( strict_types = 1 );

namespace DuckDev\FourNotFour\Api;

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

use DuckDev\FourNotFour\Utils\Helpers;

/**
 * Class Filter_Mapper
 *
 * @since   4.0.2
 * @package DuckDev\FourNotFour\Api
 */
class Filter_Mapper {

	const KIND_TEXT_SEARCH = 'text_search';
	const KIND_TEXT_ENUM   = 'text_enum';
	const KIND_INT_NUMERIC = 'int_numeric';
	const KIND_INT_ENUM    = 'int_enum';
	const KIND_BOOL        = 'bool';
	const KIND_IP          = 'ip';

	/**
	 * Operator sets per field-kind.
	 *
	 * @since 4.0.2
	 * @var array<string, string[]>
	 */
	private const OPERATORS = array(
		self::KIND_TEXT_SEARCH => array( 'is', 'isNot', 'isAny', 'isNone', 'contains', 'notContains', 'startsWith' ),
		self::KIND_TEXT_ENUM   => array( 'is', 'isNot', 'isAny', 'isNone' ),
		self::KIND_INT_NUMERIC => array( 'is', 'isNot', 'isAny', 'isNone', 'lessThan', 'greaterThan', 'lessThanOrEqual', 'greaterThanOrEqual', 'between' ),
		self::KIND_INT_ENUM    => array( 'is', 'isNot', 'isAny', 'isNone' ),
		self::KIND_BOOL        => array( 'is', 'isNot' ),
		// IP addresses live in a VARBINARY column (packed via
		// `inet_pton()`). LIKE / contains over packed bytes would
		// never match user input, so only the equality / membership
		// operators are supported — values are packed at the mapper.
		self::KIND_IP          => array( 'is', 'isNot', 'isAny', 'isNone' ),
	);

	/**
	 * Per-field config: kind, plus an optional value-set for enums.
	 *
	 * Shape:
	 *   [ field => [ 'kind' => self::KIND_*, 'values' => array|null ], … ]
	 *
	 * @since 4.0.2
	 * @var array<string, array{kind:string, values:?array}>
	 */
	private array $fields;

	/**
	 * Build a mapper for the given per-field config.
	 *
	 * @since 4.0.2
	 *
	 * @param array<string, array{kind:string, values?:array}> $fields Per-field config.
	 */
	public function __construct( array $fields ) {
		$this->fields = array_map(
			static function ( $config ) {
				return array(
					'kind'   => (string) ( $config['kind'] ?? '' ),
					'values' => isset( $config['values'] ) ? (array) $config['values'] : null,
				);
			},
			$fields
		);
	}

	/**
	 * Filter mapper for the Logs endpoint.
	 *
	 * @since 4.0.2
	 *
	 * @return self
	 */
	public static function for_logs(): self {
		return new self(
			array(
				'url'    => array( 'kind' => self::KIND_TEXT_SEARCH ),
				'ref'    => array( 'kind' => self::KIND_TEXT_SEARCH ),
				'ip'     => array( 'kind' => self::KIND_IP ),
				'hits'   => array( 'kind' => self::KIND_INT_NUMERIC ),
				'status' => array(
					'kind'   => self::KIND_INT_ENUM,
					'values' => array( 0, 1, 2 ),
				),
			)
		);
	}

	/**
	 * Filter mapper for the Redirects endpoint.
	 *
	 * @since 4.0.2
	 *
	 * @return self
	 */
	public static function for_redirects(): self {
		return new self(
			array(
				'source'        => array( 'kind' => self::KIND_TEXT_SEARCH ),
				'target_url'    => array( 'kind' => self::KIND_TEXT_SEARCH ),
				'redirect_type' => array(
					'kind'   => self::KIND_INT_ENUM,
					'values' => Helpers::redirect_status_codes(),
				),
				'match_type'    => array(
					'kind'   => self::KIND_TEXT_ENUM,
					'values' => array( 'exact', 'prefix', 'regex' ),
				),
				'is_active'     => array( 'kind' => self::KIND_BOOL ),
				'hits'          => array( 'kind' => self::KIND_INT_NUMERIC ),
			)
		);
	}

	/**
	 * Apply a list of DataViews filter entries to a BerlinDB args array.
	 *
	 * Invalid entries (unknown field, unsupported operator, malformed
	 * value, value outside the enum value-set) are silently dropped —
	 * the schema layer on the REST endpoint should already have
	 * rejected anything obviously wrong, and being permissive at this
	 * layer keeps the UI usable when an operator is added on the
	 * client before the backend catches up.
	 *
	 * @since 4.0.2
	 *
	 * @param array $filters Raw `filters` param from the request.
	 * @param array $args    BerlinDB args, mutated in place.
	 *
	 * @return void
	 */
	public function apply( array $filters, array &$args ): void {
		foreach ( $filters as $filter ) {
			if ( ! is_array( $filter ) ) {
				continue;
			}

			$field    = isset( $filter['field'] ) ? (string) $filter['field'] : '';
			$operator = isset( $filter['operator'] ) ? (string) $filter['operator'] : '';
			$value    = $filter['value'] ?? null;

			if ( '' === $field || '' === $operator || ! isset( $this->fields[ $field ] ) ) {
				continue;
			}

			$kind = $this->fields[ $field ]['kind'];
			if ( ! in_array( $operator, self::OPERATORS[ $kind ] ?? array(), true ) ) {
				continue;
			}

			$this->translate( $field, $kind, $operator, $value, $args );
		}
	}

	/**
	 * Translate a single (field, operator, value) into BerlinDB args.
	 *
	 * @since 4.0.2
	 *
	 * @param string $field    Column name.
	 * @param string $kind     Field kind (KIND_*).
	 * @param string $operator DataViews operator.
	 * @param mixed  $value    Raw value from the request.
	 * @param array  $args     BerlinDB args, mutated in place.
	 *
	 * @return void
	 */
	private function translate( string $field, string $kind, string $operator, $value, array &$args ): void {
		// Boolean fields collapse `isNot true|false` to `is false|true`,
		// so the model only ever sees `field => 0|1`. Avoids needing a
		// NOT IN clause for a one-bit column.
		if ( self::KIND_BOOL === $kind ) {
			$bool = $this->to_bool( $value );
			if ( null === $bool ) {
				return;
			}
			if ( 'isNot' === $operator ) {
				$bool = ! $bool;
			}
			$args[ $field ] = $bool ? 1 : 0;
			return;
		}

		switch ( $operator ) {
			case 'is':
				$scalar = $this->coerce_scalar( $field, $kind, $value );
				if ( null === $scalar ) {
					return;
				}
				$args[ $field ] = $scalar;
				return;

			case 'isNot':
				$scalar = $this->coerce_scalar( $field, $kind, $value );
				if ( null === $scalar ) {
					return;
				}
				$args[ "{$field}__not_in" ] = array( $scalar );
				return;

			case 'isAny':
				$values = $this->coerce_list( $field, $kind, $value );
				if ( empty( $values ) ) {
					return;
				}
				$args[ "{$field}__in" ] = $values;
				return;

			case 'isNone':
				$values = $this->coerce_list( $field, $kind, $value );
				if ( empty( $values ) ) {
					return;
				}
				$args[ "{$field}__not_in" ] = $values;
				return;

			case 'lessThan':
			case 'greaterThan':
			case 'lessThanOrEqual':
			case 'greaterThanOrEqual':
				$number = $this->coerce_number( $value );
				if ( null === $number ) {
					return;
				}
				// BerlinDB ships a `compare_query` arg, but it routes
				// through WP_Meta_Query and only fires when the table
				// has a metadata sidecar — which ours don't. Use our
				// own `range_query` arg, picked up by Model::apply_extra_query().
				$args['range_query']   = $args['range_query'] ?? array();
				$args['range_query'][] = array(
					'column'  => $field,
					'value'   => $number,
					'compare' => $this->compare_symbol( $operator ),
				);
				return;

			case 'between':
				if ( ! is_array( $value ) || 2 !== count( $value ) ) {
					return;
				}
				$min = $this->coerce_number( $value[0] ?? null );
				$max = $this->coerce_number( $value[1] ?? null );
				if ( null === $min || null === $max ) {
					return;
				}
				if ( $min > $max ) {
					[ $min, $max ] = array( $max, $min );
				}
				$args['range_query']   = $args['range_query'] ?? array();
				$args['range_query'][] = array(
					'column'  => $field,
					'value'   => array( $min, $max ),
					'compare' => 'BETWEEN',
				);
				return;

			case 'contains':
			case 'notContains':
			case 'startsWith':
				if ( self::KIND_TEXT_SEARCH !== $kind ) {
					return;
				}
				$needle = is_scalar( $value ) ? (string) $value : '';
				if ( '' === $needle ) {
					return;
				}
				$args['like_query']   = $args['like_query'] ?? array();
				$args['like_query'][] = array(
					'column'  => $field,
					'value'   => $needle,
					'compare' => $this->like_compare( $operator ),
				);
				return;
		}
	}

	/**
	 * Coerce a value into the scalar type expected by the field.
	 *
	 * @since 4.0.2
	 *
	 * @param string $field Column name.
	 * @param string $kind  Field kind.
	 * @param mixed  $value Raw value.
	 *
	 * @return int|string|null
	 */
	private function coerce_scalar( string $field, string $kind, $value ) {
		if ( ! is_scalar( $value ) ) {
			return null;
		}

		if ( self::KIND_INT_NUMERIC === $kind || self::KIND_INT_ENUM === $kind ) {
			$out = (int) $value;
		} elseif ( self::KIND_IP === $kind ) {
			$packed = Helpers::pack_ip( (string) $value );
			if ( '' === $packed ) {
				return null;
			}
			return $packed;
		} else {
			$out = (string) $value;
			if ( '' === $out ) {
				return null;
			}
		}

		$set = $this->fields[ $field ]['values'] ?? null;
		if ( null !== $set && ! in_array( $out, $set, true ) ) {
			return null;
		}

		return $out;
	}

	/**
	 * Coerce a value (scalar or array) into a list of scalars valid for
	 * the field. Drops entries outside the enum value-set when one is
	 * configured.
	 *
	 * @since 4.0.2
	 *
	 * @param string $field Column name.
	 * @param string $kind  Field kind.
	 * @param mixed  $value Raw value.
	 *
	 * @return array<int|string>
	 */
	private function coerce_list( string $field, string $kind, $value ): array {
		$raw   = is_array( $value ) ? $value : array( $value );
		$out   = array();
		$set   = $this->fields[ $field ]['values'] ?? null;
		$is_i  = self::KIND_INT_NUMERIC === $kind || self::KIND_INT_ENUM === $kind;
		$is_ip = self::KIND_IP === $kind;

		foreach ( $raw as $entry ) {
			if ( ! is_scalar( $entry ) ) {
				continue;
			}

			if ( $is_ip ) {
				$coerced = Helpers::pack_ip( (string) $entry );
				if ( '' === $coerced ) {
					continue;
				}
			} elseif ( $is_i ) {
				$coerced = (int) $entry;
			} else {
				$coerced = (string) $entry;
				if ( '' === $coerced ) {
					continue;
				}
			}

			if ( ! $is_ip && null !== $set && ! in_array( $coerced, $set, true ) ) {
				continue;
			}
			$out[] = $coerced;
		}

		return array_values( array_unique( $out, SORT_REGULAR ) );
	}

	/**
	 * Coerce a numeric value, returning null for anything non-numeric.
	 *
	 * @since 4.0.2
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return int|float|null
	 */
	private function coerce_number( $value ) {
		if ( ! is_scalar( $value ) || '' === $value ) {
			return null;
		}
		if ( ! is_numeric( $value ) ) {
			return null;
		}
		$num = $value + 0;
		return is_int( $num ) || is_float( $num ) ? $num : null;
	}

	/**
	 * Normalise truthy/falsy request values into a strict bool.
	 *
	 * Accepts JSON booleans, integers, and the typical string forms
	 * REST clients send when the schema layer doesn't coerce
	 * (`"true"`, `"false"`, `"1"`, `"0"`).
	 *
	 * @since 4.0.2
	 *
	 * @param mixed $value Raw value.
	 *
	 * @return bool|null Null when the value can't be interpreted.
	 */
	private function to_bool( $value ): ?bool {
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( is_int( $value ) ) {
			return 0 !== $value;
		}
		if ( is_string( $value ) ) {
			$normalised = strtolower( trim( $value ) );
			if ( in_array( $normalised, array( 'true', '1', 'yes', 'on' ), true ) ) {
				return true;
			}
			if ( in_array( $normalised, array( 'false', '0', 'no', 'off', '' ), true ) ) {
				return false;
			}
		}
		return null;
	}

	/**
	 * Map a comparison operator name to its SQL symbol.
	 *
	 * @since 4.0.2
	 *
	 * @param string $operator DataViews operator.
	 *
	 * @return string
	 */
	private function compare_symbol( string $operator ): string {
		switch ( $operator ) {
			case 'lessThan':
				return '<';
			case 'greaterThan':
				return '>';
			case 'lessThanOrEqual':
				return '<=';
			case 'greaterThanOrEqual':
				return '>=';
		}
		return '=';
	}

	/**
	 * Map a LIKE-style operator to its `like_query` compare token.
	 *
	 * @since 4.0.2
	 *
	 * @param string $operator DataViews operator.
	 *
	 * @return string One of `contains` | `not_contains` | `starts_with`.
	 */
	private function like_compare( string $operator ): string {
		switch ( $operator ) {
			case 'notContains':
				return 'not_contains';
			case 'startsWith':
				return 'starts_with';
		}
		return 'contains';
	}
}
