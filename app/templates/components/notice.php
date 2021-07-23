<?php
/**
 * Admin notice template.
 *
 * @var string  $type    Notice type.
 * @var string  $content Notice content.
 * @var array   $options {
 *     Array of notice options.
 *
 * @type string $id      Unique ID for the notice.
 * @type bool   $icon    Should show icon.
 * @type bool   $dismiss Is notice dismissible.
 * }
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Notice
 */

// Default type id 'updated'.
$type = empty( $type ) ? 'success' : $type; // phpcs:ignore
$options = empty( $options ) ? array() : (array) $options; // phpcs:ignore
// Default options.
$default = array(
	'id'      => uniqid(),
	'icon'    => true,
	'dismiss' => true,
);
// Parse options.
$options = wp_parse_args( $options, $default );

// Prepare classes.
$classes = array( 'notice', 'tsf-notice' );
if ( $options['icon'] ) {
	$classes[] = 'tsf-show-icon';
}
if ( $options['dismiss'] ) {
	$classes[] = 'is-dismissible';
}

switch ( $type ) {
	case 'error':
		$classes[] = 'error';
		break;
	case 'info':
		$classes[] = 'notice-info';
		break;
	case 'warning':
		$classes[] = 'notice-warning';
		break;
	default:
		$classes[] = 'updated';
}

?>

<div id="dd404-notice-<?php echo esc_attr( $options['id'] ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<p><?php echo $content; // phpcs:ignore ?></p>
</div>
