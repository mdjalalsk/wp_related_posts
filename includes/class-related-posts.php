<?php
/**
 * Class Related_Posts
 *
 * A WordPress plugin to display related posts based on the current post's categories and tags.
 * This class follows the singleton pattern to ensure only one instance is created.
 *
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Related_Posts {

    /**
     * File path of the plugin main file.
     *
     * @var string $file File path.
     * @since 1.0.0
     */
    public string $file;

    /**
     * Plugin version.
     *
     * @var string $version Plugin version.
     * @since 1.0.0
     */
    public string $version;

    /**
     * Constructor for the Related_Posts class.
     *
     * Initializes the class by setting the file path and version, defining constants,
     * setting up activation hooks, and initializing WordPress hooks.
     *
     * @param string $file    The file path of the main plugin file.
     * @param string $version Optional. The version of the plugin. Default is '1.0.0'.
     *
     * @since 1.0.0
     */
    public function __construct($file, $version = '1.0.0') {
        $this->file = $file;
        $this->version = $version;
        $this->define_constant();
        $this->activation();
        $this->init_hooks();
    }

    /**
     * Defines constants used by the plugin.
     *
     * Sets up constants for plugin version, directory path, URL, and basename.
     *
     * @return void
     * @since 1.0.0
     */
    public function define_constant() {
        define('RP_VERSION', $this->version);
        define('RP_PLUGIN_DIR', __DIR__);
        define('RP_PLUGIN_URL', plugin_dir_url($this->file));
        define('RP_PLUGIN_BASENAME', plugin_basename($this->file));
    }

    /**
     * Registers the activation hook for the plugin.
     *
     * Calls the activation hook method when the plugin is activated.
     *
     * @return void
     * @since 1.0.0
     */
    public function activation() {
        register_activation_hook($this->file, array($this, 'activation_hook'));
    }

    /**
     * Activation hook callback.
     *
     * Updates the version option in the database when the plugin is activated.
     *
     * @return void
     * @since 1.0.0
     */
    public function activation_hook() {
        update_option('rp_version', $this->version);
    }

    /**
     * Initializes WordPress hooks for the plugin.
     *
     * Registers hooks for plugin text domain and other actions.
     *
     * @return void
     * @since 1.0.0
     */
    public function init_hooks() {
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('wp', [$this, 'display_related_posts']);
    }

    /**
     * Loads the text domain for plugin translations.
     *
     * This method ensures that translations are loaded for the plugin text domain.
     *
     * @return void
     * @since 1.0.0
     */
    public function load_textdomain() {
        load_plugin_textdomain('related-posts', false, dirname(plugin_basename($this->file)) . '/languages');
    }

    /**
     * Enqueue plugin styles.
     *
     * Enqueues the CSS styles for the related posts.
     *
     * @since 1.0.0
     * @return void
     */
    public function enqueue_styles() {
        wp_enqueue_style('related-posts-style', RP_PLUGIN_URL . 'assets/css/style.css', array(), RP_VERSION);
    }

    /**
     * Displays related posts on single post pages.
     *
     * Adds related posts to the content of single posts based on categories and tags.
     *
     * @return void
     * @since 1.0.0
     */
    public function display_related_posts() {
        if (is_single() && !is_admin()) {
            add_filter('the_content', array($this, 'append_related_posts'));
        }
    }

    /**
     * Appends related posts to the content of the current post.
     *
     * Filters the post content to include related posts based on the current post's categories and tags.
     *
     * @param string $content The original content of the post.
     * @return string Modified content with related posts appended.
     * @since 1.0.0
     */
    public function append_related_posts($content) {
        $related_posts = $this->get_related_posts();
        if ($related_posts->have_posts()) {
            ob_start();

            echo '<h3 class="related-heading">' . esc_html__('Related Posts', 'related-posts') . '</h3>';
            echo '<ul class="related-posts">';
            while ($related_posts->have_posts()) {
                $related_posts->the_post();
                $this->render_related_post_item();
            }
            echo '</ul>';
            wp_reset_postdata();
            $content .= ob_get_clean();
        } else {
            // Optionally, handle cases where no related posts are found.
            $content .= '<p>' . esc_html__('No related posts found.', 'related-posts') . '</p>';
        }
        return $content;
    }

    /**
     * Retrieves related posts based on the current post's categories.
     *
     * Queries posts that share categories with the current post, excluding the current post.
     *
     * @return WP_Query The query object containing related posts.
     * @since 1.0.0
     */
    private function get_related_posts() {
        $post_id = get_the_ID();
        $categories = wp_get_post_categories($post_id);
        if (empty($categories)) {
            return new WP_Query();
        }

        $args = [
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'category__in'        => $categories,
            'post__not_in'        => [$post_id],
            'posts_per_page'      => 5,
            'orderby'             => 'date', // Sorting by date as a more efficient method
            'ignore_sticky_posts' => true
        ];

        return new WP_Query($args);
    }

    /**
     * Renders a single related post item.
     *
     * Outputs HTML for a single related post, including a link to the post and a thumbnail.
     * Proper escaping is applied to ensure security.
     *
     * @return void
     * @since 1.0.0
     */
    private function render_related_post_item() {
        $post_id = get_the_ID();
        $post_title = get_the_title();
        $post_permalink = get_permalink();
        $post_thumbnail = has_post_thumbnail() ? get_the_post_thumbnail_url($post_id, 'medium') : ''; // Use 'medium' for better quality
        $post_date = get_the_date();
        $post_author = get_the_author();
        $post_content = get_the_content();
        $post_content_trimmed = wp_trim_words($post_content, 20, '...');

        echo '<li class="related-post-card">';
        echo '<a href="' . esc_url($post_permalink) . '" title="' . esc_attr($post_title) . '" class="related-post-link">';

        if ($post_thumbnail) {
            echo '<div class="related-post-thumbnail" style="background-image: url(' . esc_url($post_thumbnail) . ');"></div>';
        }

        echo '<div class="related-post-content">';
        echo '<h4 class="related-post-title">' . esc_html($post_title) . '</h4>';
        echo '<p>' . esc_html($post_content_trimmed) . '</p>';
        echo '<p class="related-post-meta">';
        echo '<span class="related-post-date">' . esc_html($post_date) . '</span>';
        echo ' by <span class="related-post-author">' . esc_html($post_author) . '</span>';
        echo '</p>';
        echo '</div>';
        echo '</a>';
        echo '</li>';
    }

}
