<?php
/*
Plugin Name: Song Lyrics
Plugin URI: http://efj.org.in
Description: A plugin to manage and display song lyrics with search and copy functionality.
Version: 1.0
Author: Everything For Jesus
Author URI: http://efj.org.in
License: MIT
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Register Custom Post Type
function slp_register_songs_post_type() {
    $labels = array(
        'name' => _x('Songs', 'Post Type General Name', 'textdomain'),
        'singular_name' => _x('Song', 'Post Type Singular Name', 'textdomain'),
        'menu_name' => __('Songs', 'textdomain'),
        'name_admin_bar' => __('Song', 'textdomain'),
        'add_new' => __('Add New', 'textdomain'),
        'add_new_item' => __('Add New Song', 'textdomain'),
        'new_item' => __('New Song', 'textdomain'),
        'edit_item' => __('Edit Song', 'textdomain'),
        'view_item' => __('View Song', 'textdomain'),
        'all_items' => __('All Songs', 'textdomain'),
        'search_items' => __('Search Songs', 'textdomain'),
        'not_found' => __('No songs found.', 'textdomain'),
        'not_found_in_trash' => __('No songs found in Trash.', 'textdomain'),
    );

    $args = array(
        'label' => __('Song', 'textdomain'),
        'labels' => $labels,
        'supports' => array('title'), // Remove 'editor' to hide the standard description box
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'songs'),
        'exclude_from_search' => false,
        'publicly_queryable' => true,
        'capability_type' => 'post',
    );

    register_post_type('songs', $args);
}
add_action('init', 'slp_register_songs_post_type', 0);

// Flush rewrite rules on activation
function slp_rewrite_flush() {
    slp_register_songs_post_type();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'slp_rewrite_flush');

// Add Meta Boxes for Chorus and Stanzas
function slp_add_meta_boxes() {
    add_meta_box('slp_chorus', 'Chorus', 'slp_chorus_callback', 'songs', 'normal', 'high');
    add_meta_box('slp_stanzas', 'Stanzas', 'slp_stanzas_callback', 'songs', 'normal', 'high');
}
add_action('add_meta_boxes', 'slp_add_meta_boxes');

function slp_chorus_callback($post) {
    $chorus = get_post_meta($post->ID, '_slp_chorus', true);
    echo '<textarea style="width:100%; height:100px;" name="slp_chorus">' . esc_textarea($chorus) . '</textarea>';
}

function slp_stanzas_callback($post) {
    $stanzas = get_post_meta($post->ID, '_slp_stanzas', true);
    if (!is_array($stanzas)) {
        $stanzas = array('');
    }
    echo '<div id="slp_stanzas_wrapper">';
    foreach ($stanzas as $stanza) {
        echo '<textarea style="width:100%; height:100px; margin-bottom:10px;" name="slp_stanzas[]">' . esc_textarea($stanza) . '</textarea>';
    }
    echo '</div>';
    echo '<button type="button" id="slp_add_stanza_button">Add Stanza</button>';
}

function slp_save_meta_boxes($post_id) {
    if (array_key_exists('slp_chorus', $_POST)) {
        update_post_meta($post_id, '_slp_chorus', sanitize_textarea_field($_POST['slp_chorus']));
    }
    if (array_key_exists('slp_stanzas', $_POST)) {
        $stanzas = array_map('sanitize_textarea_field', $_POST['slp_stanzas']);
        update_post_meta($post_id, '_slp_stanzas', $stanzas);
    }
}
add_action('save_post', 'slp_save_meta_boxes');

function slp_enqueue_admin_scripts() {
    global $typenow;
    if ($typenow == 'songs') {
        wp_enqueue_script('slp-admin-script', plugin_dir_url(__FILE__) . 'js/slp-admin-script.js', array('jquery'), null, true);
    }
}
add_action('admin_enqueue_scripts', 'slp_enqueue_admin_scripts');

// Enqueue plugin styles
function slp_enqueue_styles() {
    wp_enqueue_style('slp-styles', plugin_dir_url(__FILE__) . 'css/style.css');
}
add_action('wp_enqueue_scripts', 'slp_enqueue_styles');

// Hook to use custom template for songs post type
add_filter('template_include', 'slp_include_template_function', 1);

function slp_include_template_function($template_path) {
    if (get_post_type() == 'songs') {
        // Check if the custom template exists in the plugin directory
        $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-songs.php';
        if (file_exists($plugin_template)) {
            return $plugin_template;
        }
    }
    return $template_path;
}

// Shortcode for displaying songs
function slp_display_songs() {
    ob_start();
    ?>

    <div class="songs-container">
        <h1>Search Songs</h1>
        <form method="get" id="searchform" action="">
            <input type="text" name="s" id="s" placeholder="Search songs..." />
            <input type="hidden" name="post_type" value="songs" />
            <input type="submit" id="searchsubmit" value="Search" />
        </form>

        <div class="alphabet-list">
            <?php
            $telugu_alphabet = array('అ', 'ఆ', 'ఇ', 'ఈ', 'ఉ', 'ఊ', 'ఋ', 'ఎ', 'ఏ', 'ఐ', 'ఒ', 'ఓ', 'ఔ', 'క', 'ఖ', 'గ', 'ఘ', 'చ', 'ఛ', 'జ', 'ఝ', 'ట', 'ఠ', 'డ', 'ఢ', 'త', 'థ', 'ద', 'ధ', 'న', 'ప', 'ఫ', 'బ', 'భ', 'మ', 'య', 'ర', 'ల', 'వ', 'శ', 'ష', 'స', 'హ', 'ళ', 'క్ష', 'ఱ');
            foreach ($telugu_alphabet as $letter) : ?>
                <a href="?alpha=<?php echo urlencode($letter); ?>"><?php echo $letter; ?></a>
            <?php endforeach; ?>
        </div>

        <?php
        if (isset($_GET['s'])) {
            $args = array(
                'post_type' => 'songs',
                's' => sanitize_text_field($_GET['s'])
            );
            $query = new WP_Query($args);

            if ($query->have_posts()) {
                while ($query->have_posts()) : $query->the_post(); ?>
                    <div class="song">
                        <h2><?php the_title(); ?></h2>
                        <?php
                        $chorus = get_post_meta(get_the_ID(), '_slp_chorus', true);
                        $stanzas = get_post_meta(get_the_ID(), '_slp_stanzas', true);
                        ?>
                        <div class="chorus">
                            <h3>Chorus</h3>
                            <p><?php echo nl2br(esc_html($chorus)); ?></p>
                        </div>
                        <button class="copy-btn" data-target=".chorus">Copy Chorus</button>
                        <?php
                        if ($stanzas) : ?>
                            <div class="stanzas">
                                <h3>Stanzas</h3>
                                <?php foreach ($stanzas as $stanza) : ?>
                                    <p><?php echo nl2br(esc_html($stanza)); ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <button class="copy-btn" data-target=".stanzas">Copy Stanzas</button>
                        <button class="share-btn" data-url="<?php the_permalink(); ?>">Share</button>
                    </div>
                <?php endwhile;
            } else {
                echo '<p>No songs found</p>';
            }
        }

        if (isset($_GET['alpha'])) {
            $args = array(
                'post_type' => 'songs',
                'orderby' => 'title',
                'order' => 'ASC',
                'meta_query' => array(
                    array(
                        'key' => '_slp_chorus',
                        'value' => '^' . sanitize_text_field($_GET['alpha']),
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
        }
        ?>
    </div>

    <script>
    document.querySelectorAll('.copy-btn').forEach(button => {
        button.addEventListener('click', () => {
            const target = button.parentElement.querySelector(button.dataset.target).innerText;
            navigator.clipboard.writeText(target).then(() => {
                alert('Copied to clipboard');
            });
        });
    });

    document.querySelectorAll('.share-btn').forEach(button => {
        button.addEventListener('click', () => {
            const url = button.dataset.url;
            if (navigator.share) {
                navigator.share({
                    title: 'Check out this song',
                    url: url
                }).then(() => {
                    console.log('Thanks for sharing!');
                }).catch(console.error);
            } else {
                alert('Share not supported on this browser');
            }
        });
    });
    </script>

    <?php
    return ob_get_clean();
}
add_shortcode('display_songs', 'slp_display_songs');
