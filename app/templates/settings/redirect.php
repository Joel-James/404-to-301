<?php
/**
 * Admin settings redirect tab template.
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
	<label for="enable">
		<input type="checkbox" name="enable" id="enable" value="1" checked> Enable redirect?
	</label>
</div>

<hr>

<h4><?php esc_html_e( 'Redirect type', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>

<div class="tsf-fields">
	<p><label><input type="radio" name="type" value="1" checked> <?php esc_html_e( '301 Redirect', '404-to-301' ); ?></label></p>
	<p><label><input type="radio" name="type" value="2"> <?php esc_html_e( '302 Redirect', '404-to-301' ); ?></label></p>
	<p><label><input type="radio" name="type" value="3"> <?php esc_html_e( '307 Redirect', '404-to-301' ); ?></label></p>
</div>

<hr>

<h4><?php esc_html_e( 'Target', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>

<div class="tsf-fields">
	<p><label><input type="radio" name="target" value="1" checked> <?php esc_html_e( 'Select an existing page', '404-to-301' ); ?></label></p>
	<p><label><input type="radio" name="target" value="2"> <?php esc_html_e( 'Enter a custom URL', '404-to-301' ); ?></label></p>
</div>


<div class="tsf-fields">
	<p><label for="page"><strong><?php esc_html_e( 'Select page', '404-to-301' ); ?></strong></label></p>
	<p>
		<?php
		wp_dropdown_pages(
			array(
				'name'     => 'page',
				'selected' => 'home',
			)
		);
		?>
	</p>
	<p class="description"><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>
</div>

<p><label for="url"><strong><?php esc_html_e( 'Custom URL', '404-to-301' ); ?></strong></label></p>
<p><input type="url" name="url" id="url"></p>
<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>
