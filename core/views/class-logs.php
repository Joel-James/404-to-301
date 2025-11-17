<?php
/**
 * The plugin logs page view class.
 *
 * @since      4.0.0
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Logs
 */

namespace DuckDev\FourNotFour\Views;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Class Logs
 *
 * @extends View
 * @since   4.0.0
 * @package DuckDev\FourNotFour\Views
 */
class Logs extends View {

	/**
	 * Content for logs page.
	 *
	 * Render the template file for error logs page with data.
	 *
	 * @since  4.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function content() {
		// Admin logs template.
		$this->render(
			'logs',
			array(
				'filters'        => $this->get_filters(),
				'pagination'     => $this->get_pagination(),
				'bulk_actions'   => $this->get_bulk_actions(),
				'filter_actions' => $this->get_filter_actions(),
			)
		);
	}

	/**
	 * Filters for logs listing page.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_filters() {
		$filters = array(
			'filters' => array(
				'all'        => array(
					'label' => _x( 'All', 'logs_filter', '404-to-301' ),
					'count' => 10,
				),
				'customised' => array(
					'label' => _x( 'Customised', 'logs_filter', '404-to-301' ),
					'count' => 5,
				),
			),
			'label'   => __( 'Filter logs list', '404-to-301' ),
			'current' => $this->get_param( 'filter', 'all' ),
		);

		/**
		 * Filter hook to modify the logs top filters.
		 *
		 * @param array $filters Filters.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_logs_view_filters', $filters );
	}

	/**
	 * Bulk actions list for logs page.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', '404-to-301' ),
		);

		/**
		 * Filter hook to modify the logs list bulk actions.
		 *
		 * @param array $actions Actions.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_logs_view_bulk_actions', $actions );
	}

	/**
	 * Filter actions list for logs page.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_filter_actions() {
		$actions = array(
			'group_by' => array(
				'label'        => __( 'Group by', '404-to-301' ),
				'submit_label' => __( 'Apply', '404-to-301' ),
				'options'      => array(
					''     => __( 'Group', '404-to-301' ),
					'path' => __( '404 Path', '404-to-301' ),
					'ref'  => __( 'Referrer', '404-to-301' ),
					'ua'   => __( 'User Agent', '404-to-301' ),
					'ip'   => __( 'IP Address', '404-to-301' ),
				),
			),
		);

		/**
		 * Filter hook to modify the logs list filter actions.
		 *
		 * @param array $actions Actions.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_logs_view_filter_actions', $actions );
	}

	/**
	 * Pagination data for logs page.
	 *
	 * @since  4.0.0
	 * @access private
	 *
	 * @return array
	 */
	private function get_pagination() {
		$pagination = array(
			'total'   => 15,
			'current' => 1,
			'pages'   => 2,
		);

		/**
		 * Filter hook to modify the logs list pagination.
		 *
		 * @param array $pagination Pagination data.
		 *
		 * @since 4.0.0
		 */
		return apply_filters( '404_to_301_logs_view_pagination', $pagination );
	}
}
