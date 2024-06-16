### Files and Their Functionality

#### `song-lyrics-plugin.php`

This is the main plugin file that contains the core functionality of the plugin.

- **Plugin Header:** Contains the plugin name, URI, description, version, author, and license information.
- **Custom Post Type Registration:** Registers the `songs` custom post type with necessary labels and arguments.
- **Rewrite Flush:** Flushes rewrite rules on activation to ensure the custom post type works.
- **Meta Boxes:** Adds meta boxes for `Chorus` and `Stanzas` to the `songs` post type.
- **Meta Boxes Callbacks:** Provides the callback functions to display and save the meta boxes.
- **Enqueue Scripts and Styles:** Enqueues admin scripts for handling meta box functionality and public styles for frontend display.
- **Template Include Hook:** Ensures that the custom template file from the plugin is used for displaying single `songs` posts.
- **Shortcode:** Provides a shortcode `[display_songs]` for displaying songs on any page or post.

##### **Customizations:**

1. **Custom Post Type Registration:**

   - Customize the labels and arguments as per your requirements.

   ```php
   'label' => __('Song', 'textdomain'),
   'labels' => $labels,
   'supports' => array('title'), // Add 'editor' if you want to use the standard description box
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
   ```

2. **Meta Boxes:**

   - Add or modify meta boxes as needed.

   ```php
   add_meta_box('slp_chorus', 'Chorus', 'slp_chorus_callback', 'songs', 'normal', 'high');
   add_meta_box('slp_stanzas', 'Stanzas', 'slp_stanzas_callback', 'songs', 'normal', 'high');
   ```

3. **Enqueue Styles and Scripts:**
   - Modify or add styles and scripts as required.
   ```php
   wp_enqueue_style('slp-styles', plugin_dir_url(__FILE__) . 'css/style.css');
   wp_enqueue_script('slp-admin-script', plugin_dir_url(__FILE__) . 'js/slp-admin-script.js', array('jquery'), null, true);
   ```

#### `css/style.css`

This file contains the CSS styles for the plugin.

- **Alphabet List:** Styles for the alphabet navigation list.
- **Chorus and Stanzas:** Styles for displaying the chorus and stanzas of a song.
- **Selected Class:** Styles for highlighting the selected alphabet letter.

##### **Customizations:**

1. **Alphabet List:**

   - Modify the appearance of the alphabet list.

   ```css
   .alphabet-list {
     display: flex;
     flex-wrap: wrap;
     gap: 10px;
     justify-content: center;
   }

   .alphabet-list a {
     display: inline-flex;
     align-items: center;
     justify-content: center;
     width: 50px;
     height: 50px;
     background-color: #f1f1f1;
     color: #007cba;
     text-decoration: none;
     font-size: 24px;
     border-radius: 4px;
     transition: background-color 0.3s, color 0.3s;
   }

   .alphabet-list a:hover {
     background-color: #007cba;
     color: #fff;
   }

   .alphabet-list a.selected {
     background-color: #007cba;
     color: #fff;
   }
   ```

2. **Chorus and Stanzas:**
   - Modify the styles for chorus and stanzas.
   ```css
   .chorus,
   .stanzas {
     margin-top: 20px;
   }
   .chorus p,
   .stanzas p {
     margin: 10px 0;
   }
   ```

#### `js/slp-admin-script.js`

This file contains the JavaScript for handling dynamic functionality in the admin area.

- **Add Stanza Button:** Adds new stanza fields dynamically in the meta box.

##### **Customizations:**

1. **Add Stanza Functionality:**
   - Modify the functionality to suit your needs.
   ```js
   jQuery(document).ready(function ($) {
     $("#slp_add_stanza_button").on("click", function () {
       var stanzaField =
         '<textarea style="width:100%; height:100px; margin-bottom:10px;" name="slp_stanzas[]"></textarea>';
       $("#slp_stanzas_wrapper").append(stanzaField);
     });
   });
   ```

#### `templates/single-songs.php`

This is the custom template file for displaying single `songs` posts.

- **Alphabet List:** Displays the alphabet navigation list at the top.
- **Song List:** Displays a list of song titles filtered by the selected alphabet letter.
- **Single Song View:** Displays the full details of a single song when viewing a specific song post.

##### **Customizations:**

1. **Alphabet List:**

   - Customize the list of letters or add more filters.

   ```php
   $telugu_alphabet = array('అ', 'ఆ', 'ఇ', 'ఈ', 'ఉ', 'ఊ', 'ఋ', 'ఎ', 'ఏ', 'ఐ', 'ఒ', 'ఓ', 'ఔ', 'క', 'ఖ', 'గ', 'ఘ', 'చ', 'ఛ', 'జ', 'ఝ', 'ట', 'ఠ', 'డ', 'ఢ', 'త', 'థ', 'ద', 'ధ', 'న', 'ప', 'ఫ', 'బ', 'భ', 'మ', 'య', 'ర', 'ల', 'వ', 'శ', 'ష', 'స', 'హ', 'ళ', 'క్ష', 'ఱ');
   foreach ($telugu_alphabet as $letter) {
       $class = ($letter === $selected_letter) ? 'selected' : '';
       echo '<a href="' . get_post_type_archive_link('songs') . '?alpha=' . urlencode($letter) . '" class="' . $class . '">' . $letter . '</a>';
   }
   ```

2. **Song Listing:**

   - Customize the song listing layout.

   ```php
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

       wp_reset_postdata();
   } else {
       if (is_singular('songs')) {
           while (have_posts()) : the_post(); ?>
               <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                   <header class="entry-header">
                       <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                   </header>

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
                   </div>

                   <footer class="entry-footer">
                       <?php // Display post metadata (categories, tags, etc.) ?>
                   </footer>
               </article>
           <?php endwhile;
       } else {
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

           wp_reset_postdata();
       }
   }
   ```

### Usage

1. **Installation:**

   - Upload the `song-lyrics-plugin` folder to the `/wp-content/plugins/` directory.
   - Activate the plugin through the 'Plugins' menu in WordPress.

2. **Adding Songs:**

   - Navigate to 'Songs' in the WordPress admin menu.
   - Click 'Add New' to create a new song.
   - Fill in the song title, chorus, and stanzas.
   - Save the song.

3. **Displaying Songs:**
   - Use the shortcode `[display_songs]` to display the song search and listing functionality on any page or post.
4. ** Screenshots**
   - <img width="332" alt="Screenshot 2024-06-16 at 8 35 42 PM" src="https://github.com/efjsocial/song-lyrics-plugin/assets/150701483/6d52b2bf-7a5c-4e8f-978b-e107d9f71cda">
   - <img width="1166" alt="Screenshot 2024-06-16 at 8 36 09 PM" src="https://github.com/efjsocial/song-lyrics-plugin/assets/150701483/3073cf77-0514-4d83-9b85-82017e49ea45">
   - <img width="686" alt="Screenshot 2024-06-16 at 8 35 57 PM" src="https://github.com/efjsocial/song-lyrics-plugin/assets/150701483/03d6beb2-56b0-4f8e-997d-31866e8d9c3c">




### License

This plugin is licensed under the MIT license.

### Author

[Everything For Jesus](http://efj.org.in)
