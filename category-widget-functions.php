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
 * Get Widget Posts
 * -----------------------------------------------------------------------------
 * @param   object      $category           The category.
 * @param   int         $count              Number of posts to fetch.
 */

function bh_category_posts($category, $count) {
    $trans_name = 'bh_category_posts_' . $category->slug;
    
    if (false === ($posts = get_transient($trans_name))) {
        $posts = get_posts(array(
            'numberposts' => $count,
            'order' => 'DESC',
            'category' => $category->cat_ID
        ));

        set_transient($trans_name, $posts, get_option('kaitain_transient_timeout')); 
    }

    return $posts;
}

/**
 * Category Widget Section Colour Trim Classes
 * -----------------------------------------------------------------------------
 * @param   object      $category           The category.
 * @return  array       $trim               Section trim CSS classes.
 */

function bh_category_trim_classes($category) {
    global $sections;
    $section = $sections->get_category_section_slug($category);

    $classes = array(
        'text' => 'section--' . $section . '--text',
        'texthover' => 'section--' . $section . '--text-hover',
        'bg' => 'section--' . $section . '--bg',
        'bghover' => 'section--' . $section . '--bg-hover'
    );

    return $classes;
}

/**
 * Category Widget Output
 * -----------------------------------------------------------------------------
 * Output the one-left-three right output of the category widget. This is in a 
 * separate function as it also used in archives.
 * 
 * @maram   int/string      $category       Widget category.
 * @param   bool            $show_name      Show name of and link to category.
 * @param   int             $count          Number of posts to output.
 */

function bh_category_widget_output($category, $show_name = true, $count = 4, $limit = 12) {
    if (!($category = get_category($category))) {
        return;
    }
    $classes = array(
        // Main CSS classes.
        'widget' => 'widget--category',
        'title' => 'widget__title vspace--half',
        'link' => 'widget--category__link',
        'split' => 'widget--category__split flex--two-col--div',
        'side_left' => 'widget--category__left',
        'side_right' => 'widget--category__right',
    );

    // Trim classes.
    $trim = bh_category_trim_classes($category);
    // Category posts for output.
    $posts = bh_category_posts($category, $count);

    printf('<div class="%s">', $classes['widget']);
        
    if ($show_name) {
        // Category name and link.
        printf('<h2 class="%s %s"><a class="%s" title="%s" href="%s">%s</a></h2>', 
            $classes['title'],
            $trim['text'],
            $classes['link'],
            $category->cat_name,
            get_category_link($category),
            $category->cat_name
        );
    }

    // Main interior container.
    printf('<div class="%s">', $classes['split']);

    foreach ($posts as $index => $post) { 
        $trim_class = array();
        // "Side" posts only need athumbnail size image.
        $image_size = 'tc_home_category_small';

        if (!$index) {
            // First post has a different layout, in a different position.
            $image_size = 'tc_home_category_lead';
            printf('<div class="%s">', $classes['side_left']);
        }

        // Left: solid background. Right: color on hover.
        $trim_class['bg'] = (!$index) ? $trim['bg'] : '';
        $trim_class['text'] = (!$index) ? '' : $trim['texthover'];

        bh_category_article_output($post, $image_size, $trim_class, $index, $limit);

        if (!$index) { 
            // If left side, close and open right.
            printf('</div>');
            printf('<div class="%s">', $classes['side_right']);
        }
    }

    // Close right side.
    printf('</div>');
    // Close widget interior.
    printf('</div>');
    // Close widget.
    printf('</div>');

    printf('<hr>');

    wp_reset_postdata();
}

/**
 * Category Article Output
 * -----------------------------------------------------------------------------
 * Articles on either side of the widget have the same HTML, but different
 * classes. I separated this for my sanity.
 *
 * @param   object          $post_id        The post object.
 * @param   string          $image_size     Thumbnail image size.
 * @param   array           $trim_class     Classes for article elements.
 */

function bh_category_article_output($post_id, $image_size, $trim_class, $index, $limit) {
    global $post;
    $post = $post_id;
    setup_postdata($post);
    
    ?>
    <?php
        if ( !$index && get_field('youtube_embed_code') ) {   ?>
            <article <?php post_class('article--category youtube-embed-post'); ?> id="article--category--<?php the_ID(); ?>">            
                <a class="article--category__link <?php printf($trim_class['text']); ?>" href="<?php the_permalink(); ?>" rel="bookmark">
                    <div class="article--category__thumb thumbnail youtube-embed-container">
                        <?php   the_field('youtube_embed_code'); ?>
                    </div>
    <?php
        } else { ?>
            <article <?php post_class('article--category'); ?> id="article--category--<?php the_ID(); ?>">
                <a class="article--category__link <?php printf($trim_class['text']); ?>" href="<?php the_permalink(); ?>" rel="bookmark"> 
                    <div class="article--category__thumb thumbnail">
                        <?php post_image_html(get_the_ID(), $image_size, true); ?>
                    </div>
    <?php
        } ?>
            <div class="article--category__text">
                <h5 class="article--category__title title <?php printf($trim_class['bg']); ?>">
                    <?php
                    if ($index === 1 || $index === 2 || $index === 3 ) {
                        printf(kaitain_excerpt(get_the_title(), $limit)); 
                    } else {
                        the_title();
                    }
                    ?>
                </h5>
            </div>
        </a>
    </article>
    <?php
} 
?>
