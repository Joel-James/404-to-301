<?php
/**
 * Admin settings general tab template.
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

<h4><?php esc_html_e( 'Enable', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>
<div class="tsf-fields">
	<span class="tsf-toblock">
		<label for="enable">
			<input type="checkbox" name="enable" id="enable" value="1" checked="checked"> Enable logs?
		</label>
	</span>
</div>

<hr/>

<h4><?php esc_html_e( 'Duplicates', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>
<div class="tsf-fields">
	<span class="tsf-toblock">
		<label for="duplicates">
			<input type="checkbox" name="duplicates" id="duplicates" value="1" checked="checked"> Skip duplicates?
		</label>
	</span>
</div>