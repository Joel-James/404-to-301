<?php
/**
 * Bulk actions list template.
 *
 * @var array $actions Actions list.
 * @var bool  $bottom     Is bottom actions.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Actions
 */

$suffix = empty( $bottom ) ? 'top' : 'bottom';

?>
<?php if ( ! empty( $actions ) ) : ?>
	<div class="alignleft actions bulkactions">
		<label for="bulk-action-selector-<?php echo esc_attr( $suffix ); ?>" class="screen-reader-text">
			<?php esc_html_e( 'Select bulk action', '404-to-301' ); ?>
		</label>
		<select name="action" id="bulk-action-selector-<?php echo esc_attr( $suffix ); ?>">
			<option value="-1"><?php esc_html_e( 'Bulk actions', '404-to-301' ); ?></option>
			<?php foreach ( $actions as $name => $label ) : ?>
				<option value="<?php echo esc_html( $name ); ?>" class="hide-if-no-js">
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<input
			type="submit"
			class="button action"
			value="<?php esc_html_e( 'Apply', '404-to-301' ); ?>"
		>
	</div>
<?php endif; ?>
