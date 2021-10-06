<?php
/**
 * Filter actions list template.
 *
 * @var array $actions Actions list.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Filters
 */

?>
<?php if ( ! empty( $actions ) ) : ?>
	<?php foreach ( $actions as $name => $data ) : ?>
		<div class="alignleft actions">
			<label class="screen-reader-text" for="cat">
				<?php echo esc_html( $data['label'] ); ?>
			</label>
			<select name="<?php echo esc_html( $name ); ?>" class="postform">
				<?php foreach ( $data['options'] as $option => $label ) : ?>
					<option value="<?php echo esc_html( $option ); ?>">
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<input
				type="submit"
				name="filter_action"
				id="post-query-submit"
				class="button"
				value="<?php echo esc_html( $data['submit_label'] ); ?>"
			>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
