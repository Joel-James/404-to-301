<?php
/**
 * Admin notice template.
 *
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @var string  $type    Notice type.
 * @var string  $content Notice content.
 * @var array   $options {
 *     Array of notice options.
 *      @type string $id      Unique ID for the notice.
 *      @type bool   $icon    Should show icon.
 *      @type bool   $dismiss Is notice dismissible.
 * }
 *
 * @copyright  Copyright (c) 2020, Joel James
 * @package    View
 * @subpackage Notice
 */

// Default type id 'updated'.
$type    = empty( $type ) ? 'success' : $type;
$options = empty( $options ) ? array() : (array) $options;
// Default options.
$default = array(
	'id'      => uniqid(),
	'icon'    => true,
	'dismiss' => true,
);
// Parse options.
$options = wp_parse_args( $options, $default );

// Prepare classes.
$classes = array( 'notice', 'duckdev-notice' );
if ( $options['icon'] ) {
	$classes[] = 'duckdev-show-icon';
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

<div id="dd4t3-notice-<?php echo esc_attr( $options['id'] ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<p><?php echo $content; ?></p>
</div>
