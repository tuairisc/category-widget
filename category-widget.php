<?php 

/**
 * Side-by-side Category Widget
 * -----------------------------------------------------------------------------
 * @category   PHP Script
 * @package    Category Widget
 * @author     Mark Grealish <mark@bhalash.com>
 * @copyright  Copyright (c) 2015 Mark Grealish
 * @license    https://www.gnu.org/copyleft/gpl.html The GNU GPL v3.0
 * @version    1.0
 * @link       https://github.com/bhalash/category-widget
 */

if (!defined('ABSPATH')) {
    die('-1');
}

require_once(dirname(__FILE__) . '/category-widget-functions.php');

if (!function_exists('set_fallback_image')) {
    // Import article images if it doesn't exist.
    require_once(dirname(__FILE__) . '/article-images/article-images.php');
}

require_once(dirname(__FILE__) . '/widget.php');

function bh_register_widget() {
    register_widget('Bhalash_Category_Widget');
}

add_action('widgets_init', 'bh_register_widget');

?>
