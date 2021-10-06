<?php
/**
 * Search box template.
 *
 * @var string $label Label.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Search
 */

// Get label.
$label = empty( $label ) ? __( 'Search', '404-to-301' ) : $label;

?>
<p class="search-box">
	<label class="screen-reader-text" for="search-input">
		<?php echo esc_html( $label ); ?>:
	</label>
	<input type="search" id="search-input" name="s" value="">
	<input
		type="submit"
		id="search-submit"
		class="button"
		value="<?php echo esc_html( $label ); ?>"
	>
</p>
