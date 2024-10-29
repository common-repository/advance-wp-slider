<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class Awp_Slider_Admin
 */
class Awp_Slider_Admin {

    private static $instance = null;

    /**
     * Get the instance of the admin class.
     *
     * @return Awp_Slider_Admin
     */
    public static function get_instance(): Awp_Slider_Admin {
        if ( ! self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Initialize global hooks.
     */
    public function init() {
        // Register settings submenu
        add_action( 'admin_menu', [ $this, 'register_setting_menu' ] );

        // Add meta boxes
        add_action( 'add_meta_boxes', [ $this, 'add_slider_meta_boxes' ] );

        // Save meta box data
        add_action( 'save_post', [ $this, 'save_slider_meta_box_data' ] );
    }

    /**
     * Create new meta boxes.
     *
     * @return void
     */
    public function add_slider_meta_boxes() {
        add_meta_box(
            'slider-button-text',
            __( 'Slider Button Text', 'advance-wp-slider' ),
            [ $this, 'render_button_text_meta_box' ],
            'awp-slider'
        );

        add_meta_box(
            'slider-button-link',
            __( 'Slider Button Link', 'advance-wp-slider' ),
            [ $this, 'render_button_link_meta_box' ],
            'awp-slider'
        );
    }

    /**
     * Render the slider button text meta box.
     *
     * @param WP_Post $post
     * @return void
     */
    public function render_button_text_meta_box( $post ) {
        wp_nonce_field( 'slider_button_text_nonce_action', 'slider_button_text_nonce' );
        $value = get_post_meta( $post->ID, 'slider_button_text', true );
        echo '<textarea style="width:100%" id="slider_button_text" name="slider_button_text">' . esc_textarea( $value ) . '</textarea>';
    }

    /**
     * Render the slider button link meta box.
     *
     * @param WP_Post $post
     * @return void
     */
    public function render_button_link_meta_box( $post ) {
        wp_nonce_field( 'slider_button_link_nonce_action', 'slider_button_link_nonce' );
        $value = get_post_meta( $post->ID, 'slider_button_link', true );
        echo '<textarea style="width:100%" id="slider_button_link" name="slider_button_link">' . esc_textarea( $value ) . '</textarea>';
    }

    /**
     * Save the meta box data.
     *
     * @param int $post_id
     * @return void
     */

        public function save_slider_meta_box_data( $post_id ) {
            // Check if the nonce is set and valid for button text
            if ( ! isset( $_POST['slider_button_text_nonce'] ) ||
                 ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['slider_button_text_nonce'] ) ), 'slider_button_text_nonce_action' ) ) {
                return;
            }
        
            // Check if the nonce is set and valid for button link
            if ( ! isset( $_POST['slider_button_link_nonce'] ) ||
                 ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['slider_button_link_nonce'] ) ), 'slider_button_link_nonce_action' ) ) {
                return;
            }
        
        
        


        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save slider button text
        if ( isset( $_POST['slider_button_text'] ) ) {
            $text = sanitize_text_field( wp_unslash( $_POST['slider_button_text'] ) );
            update_post_meta( $post_id, 'slider_button_text', $text );
        }

        // Save slider button link
        if ( isset( $_POST['slider_button_link'] ) ) {
            $link = sanitize_text_field( wp_unslash( $_POST['slider_button_link'] ) );
            update_post_meta( $post_id, 'slider_button_link', $link );
        }
    }

    /**
     * Register submenu page.
     *
     * @return void
     */
    public function register_setting_menu() {
        add_submenu_page(
            'edit.php?post_type=awp-slider',
            __( 'Settings', 'advance-wp-slider' ),
            __( 'Settings', 'advance-wp-slider' ),
            'manage_options',
            'setting-page',
            [ $this, 'render_setting_page' ]
        );
    }

    /**
     * Render settings page.
     *
     * @return void
     */
    public function render_setting_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'How to use AWP Slider', 'advance-wp-slider' ); ?></h1>
            <h3><?php esc_html_e( 'Shortcode: [AWP-SLIDER]', 'advance-wp-slider' ); ?></h3>
            <ul>
                <li><?php esc_html_e( '1. Install the AWP Slider plugin.', 'advance-wp-slider' ); ?></li>
                <li><?php esc_html_e( '2. Activate the plugin.', 'advance-wp-slider' ); ?></li>
                <li><?php esc_html_e( '3. Add the [AWP-SLIDER] shortcode to your posts or pages.', 'advance-wp-slider' ); ?></li>
                <li><?php esc_html_e( '4. Click "Add New" to create a new slider item.', 'advance-wp-slider' ); ?></li>
                <li><?php esc_html_e( '5. Publish your item.', 'advance-wp-slider' ); ?></li>
                <li><?php esc_html_e( '6. Visit your website to see the slider in action.', 'advance-wp-slider' ); ?></li>
            </ul>
        </div>
        <?php
    }
}

// Initialize the admin class
Awp_Slider_Admin::get_instance()->init();
