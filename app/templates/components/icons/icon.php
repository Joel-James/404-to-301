<?php
/**
 * SVG icon base file.
 *
 * @var string $icon   Icon name.
 * @var int    $width  Width of the icon.
 * @var int    $height Height of the icon.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Icons
 */

?>
<svg
	xmlns="http://www.w3.org/2000/svg"
	class="flex-shrink-0 -ml-1 mr-3 h-<?php echo (int) $height; ?> w-<?php echo (int) $width; ?>"
	fill="none"
	viewBox="0 0 24 24"
	stroke="currentColor"
	width="<?php echo (int) $width; ?>"
	height="<?php echo (int) $height; ?>"
>
	<?php $this->render( "components/icons/{$icon}" ); ?>
</svg>
