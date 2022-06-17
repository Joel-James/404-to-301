<?php
/**
 * Error logs page base template.
 *
 * @var array $filters        Filters.
 * @var array $bulk_actions   Bulk actions.
 * @var array $filter_actions Filter actions.
 * @var array $pagination     Pagination data.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 *
 * @subpackage Pages
 */

?>

<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( '404 Logs', '404-to-301' ); ?>
	</h1>
	<hr class="wp-header-end">

    <div id="dd4t3-logs-app" class="dd4t3-logs-wrap"></div>

</div>

