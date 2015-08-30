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

class Bhalash_Category_Widget extends WP_Widget {
    /**
     * Widget Constructor
     * -------------------------------------------------------------------------
     */

    public function __construct() {
        parent::__construct(
            __('bhalash_home_category', 'bhalash'),
            __('Featured Category Posts', 'bhalash'),
            array(
                'description' => __('Display most-recent posts from selected category.', 'bhalash')
            )
        );
    }

    /**
     * Widget Administrative Form
     * -------------------------------------------------------------------------
     * @param array     $instance       Widget instance.
     */

    public function form($instance) {
        $defaults = array(
            // Widget defaults.
            'category' => 0,
            'show_category_name' => false
        );

        $instance = wp_parse_args($instance, $defaults);

        $categories = get_categories(array(
            'type' => 'post',
            'order' => 'ASC',
            'orderby' => 'name',
            'hide_empty' => true,
        ));

        ?>

        <ul>
            <li>
                <input type="checkbox" id="<?php printf($this->get_field_id('show_category_name')); ?>" name="<?php printf($this->get_field_name('show_category_name')); ?>" />
                <label for="<?php printf($this->get_field_id('show_category_name')); ?>"><?php _e('Show category name', 'bhalash'); ?></label>
            </li>
            <li>
            </li>
            <select id="<?php printf($this->get_field_id('category')); ?>" name="<?php printf($this->get_field_name('category')); ?>">
                <?php foreach ($categories as $index => $category) {
                    printf('<option value="%s">%s (%d)</option>', $category->cat_ID, $category->name, $category->count);
                } ?>
            </select>
        </ul>
        <script>
            jQuery('<?php printf('#%s', $this->get_field_id('show_category_name')); ?>').prop('checked', <?php printf(($instance['show_category_name']) ? 'true' : 'false'); ?>);
        jQuery('<?php printf('#%s', $this->get_field_id('category')); ?>').val('<?php printf($instance['category']); ?>');
        </script>

        <?php
    }

    /**
     * Widget Update
     * -------------------------------------------------------------------------
     * @param  array    $new_default       New default variables.
     * @param  array    $old_default       Old default variables.
     * @return array    $default           New widget settings.
     */

    function update($new_defaults, $old_defaults) {
        $defaults = array();

        $defaults['show_category_name'] = ($new_defaults['show_category_name'] === 'on');
        $defaults['category'] = intval($new_defaults['category']);

        return $defaults;
    }

    /**
     * Widget Public Display
     * -------------------------------------------------------------------------
     * @param array     $defaults         Widget default values. 
     * @param array     $instance         Widget instance arguments.
     */

    public function widget($defaults, $instance) {
        if (!empty($defaults['before_widget'])) {
            printf('%s', $defaults['before_widget']);
        }
        
        bh_category_widget_output($instance['category'], $instance['show_category_name'], 5);

        if (!empty($defaults['after_widget'])) {
            printf('%s', $defaults['after_widget']);
        }

        wp_reset_postdata();
    }
}

?>
