<?php 

/**
 * Sideby-side Category Widget Functions
 * -----------------------------------------------------------------------------
 * @category   PHP Script
 * @package    Category Widget
 * @author     Mark Grealish <mark@bhalash.com>
 * @copyright  Copyright (c) 2015 Mark Grealish
 * @license    https://www.gnu.org/copyleft/gpl.html The GNU GPL v3.0
 * @version    1.0
 * @link       https://github.com/bhalash/category-widget
 */

/**
 * Category Widget Output
 * -----------------------------------------------------------------------------
 * Output the one-left-three right output of the category widget. This is in a 
 * separate function as it also used in archives.
 * 
 * @param   int/array/string    $widget_categories  Categor{y,ies}.
 * @param   int                 $numberposts        Number of posts to output.
 */

function bh_category_widget_output($cats, $show_name = true, $count = 5) {
    global $post, $sections;
    // Category ID is appended after.
    $trans_name = 'bh_category_posts_';
    $categories = array();

    // Resolve categories down to ID.
    if (is_int($cats) || is_string($cats)) {
        $categories[] = intval($cats);
    } else if (!is_array($cats)) {
        return;
    }

    /* Double loop:
     * 1. Loop each supplied category.
     * 2. For each category, output $count posts.
     * 
     * Order of output is 1 left, $count - 1 right.
     * Left post has an excerpt.
     */

    foreach($categories as $category) {
        $category = get_category($category);

        if (!$category) {
            continue;
        }

        $cat_trans_name = $trans_name . $category->slug;

        if (!($category_posts = get_transient($cat_trans_name))) {
            $category_posts = get_posts(array(
                'numberposts' => $count,
                'order' => 'DESC',
                'category' => $category->cat_ID
            ));

            set_transient($cat_trans_name, $category_posts, get_option('kaitain_transient_timeout')); 
        }

        // Fetch section trim colours.
        $trim = $sections->get_section_slug($category);

        $trim = array(
            // Section trim class information.
            'slug' => $trim,
            'text' => sprintf('section--%s-text', $trim),
            'hover' => sprintf('section--%s-text-hover', $trim),
            'background' => sprintf('section--%s-bg', $trim)
        );

        printf('<div class="%s">', 'category-widget');
        
        if ($show_name) {
            // Category name, and link to category.
            printf('<h2 class="%s %s"><a title="%s" href="%s">%s</a></h2>', 
                $trim['text'],
                'widget-title',
                $category->cat_name,
                get_category_link($category),
                $category->cat_name
            );
        }

        printf('<div class="%s">', 'category-widget-display flex--two-col--div');

        foreach ($category_posts as $index => $post) { 
            $classes = '';
            setup_postdata($post);

            $left_class = 'bhalash-category-widget-left';
            $right_class = 'bhalash-category-widget-right';

            // "Side" posts only need athumbnail size image.
            if ($index === 0) {
                $image_size = 'tc_home_category_lead';
            } else {
                $image_size = 'tc_home_category_small';
            }

            // First post has a different layout, in a different position.
            if ($index === 0) {
                printf('<div class="%s">', $left_class);
            }

            $classes = get_post_class($classes, get_the_ID());
            $classes = implode(' ', $classes);

            $classes = array(
                'article' => $classes,
                'paragraph' => ($index === 0) ? $trim['background']: '',
                'anchor' => $trim['hover']
            );

            bh_category_article_output($classes, $image_size);

            if ($index === 0) {
                printf('</div>');
                printf('<div class="%s">', $right_class);
            }
        }

        printf('</div></div></div>');
        printf('<hr>');
    }

    wp_reset_postdata();
}

/**
 * Category Article Output
 * -----------------------------------------------------------------------------
 * Articles on either side of the widget have the same HTML, but different
 * classes. I separated this for my sanity.
 *
 * @param   int/object      $post           The post object.
 * @param   array           $classes        Classes for article elements.
 * @param   string          $image_size     Thumbnail image size.
 */

function bh_category_article_output($classes, $image_size) {
    ?> 

    <article class="category-article <?php printf($classes['article']); ?>" id="<?php the_ID(); ?>">
        <a class="category-article-link <?php printf($classes['anchor']); ?>" href="<?php the_permalink(); ?>" rel="bookmark">
            <div class="category-article-thumbnail thumbnail">
                <?php post_image_html(get_the_ID(), $image_size, true); ?>
            </div>
            <div class="post-content category-article-content">
                <p class="category-article-title <?php printf($classes['paragraph']); ?>">
                    <?php the_title(); ?>
                </p>
            </div>
        </a>
    </article>

    <?php
}

?>
