<?php
/**
 * Template for displaying single songs.
 *
 * @package Your_Songs_Plugin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
        // Display alphabet list
        echo '<div class="alphabet-list">';
        $telugu_alphabet = array('అ', 'ఆ', 'ఇ', 'ఈ', 'ఉ', 'ఊ', 'ఋ', 'ఎ', 'ఏ', 'ఐ', 'ఒ', 'ఓ', 'ఔ', 'క', 'ఖ', 'గ', 'ఘ', 'చ', 'ఛ', 'జ', 'ఝ', 'ట', 'ఠ', 'డ', 'ఢ', 'త', 'థ', 'ద', 'ధ', 'న', 'ప', 'ఫ', 'బ', 'భ', 'మ', 'య', 'ర', 'ల', 'వ', 'శ', 'ష', 'స', 'హ', 'ళ', 'క్ష', 'ఱ');
        $selected_letter = isset($_GET['alpha']) ? sanitize_text_field($_GET['alpha']) : '';
        foreach ($telugu_alphabet as $letter) {
            $class = ($letter === $selected_letter) ? 'selected' : '';
            echo '<a href="' . get_post_type_archive_link('songs') . '?alpha=' . urlencode($letter) . '" class="' . $class . '">' . $letter . '</a>';
        }
        echo '</div>';

        // Handle alphabet filtering and song listing
        if ($selected_letter) {
            $args = array(
                'post_type' => 'songs',
                'orderby' => 'title',
                'order' => 'ASC',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_slp_chorus',
                        'value' => '^' . $selected_letter,
                        'compare' => 'REGEXP'
                    )
                )
            );
            $query = new WP_Query($args);

            if ($query->have_posts()) {
                echo '<ul class="song-list">';
                while ($query->have_posts()) : $query->the_post(); ?>
                    <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php endwhile;
                echo '</ul>';
            } else {
                echo '<p>No songs found starting with that letter</p>';
            }

            // Reset post data
            wp_reset_postdata();
        } else {
            // Display the current song if viewing a single song
            if (is_singular('songs')) {
                while (have_posts()) :
                    the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <header class="entry-header">
                            <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                        </header><!-- .entry-header -->

                        <div class="entry-content">
                            <?php
                            $chorus = get_post_meta(get_the_ID(), '_slp_chorus', true);
                            $stanzas = get_post_meta(get_the_ID(), '_slp_stanzas', true);

                            if (!empty($chorus)) {
                                echo '<div class="chorus">';
                                echo '<p>' . nl2br(esc_html($chorus)) . '</p>';
                                echo '</div>';
                            }

                            if (!empty($stanzas)) {
                                echo '<div class="stanzas">';
                                foreach ($stanzas as $stanza) {
                                    echo '<p>' . nl2br(esc_html($stanza)) . '</p>';
                                }
                                echo '</div>';
                            }
                            ?>
                        </div><!-- .entry-content -->

                        <footer class="entry-footer">
                            <?php // Display post metadata (categories, tags, etc.) ?>
                        </footer><!-- .entry-footer -->
                    </article><!-- #post-<?php the_ID(); ?> -->

                <?php endwhile;
            } else {
                // Display all songs when no specific song or alphabet is selected
                $args = array(
                    'post_type' => 'songs',
                    'orderby' => 'title',
                    'order' => 'ASC',
                    'posts_per_page' => -1
                );

                $query = new WP_Query($args);

                if ($query->have_posts()) {
                    echo '<ul class="song-list">';
                    while ($query->have_posts()) : $query->the_post(); ?>
                        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                    <?php endwhile;
                    echo '</ul>';
                } else {
                    echo '<p>No songs found</p>';
                }

                // Reset post data
                wp_reset_postdata();
            }
        }
        ?>
    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
