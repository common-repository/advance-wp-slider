<?php
defined( 'ABSPATH' ) || exit;

/**
 * Class Wp_Simple_Frontend
 */
class Awp_Slider_Frontend
{

    private static $instance = null;

    /**
     * Make instance of the admin class.
     */
    public static function get_instance()
    {
        if (! self::$instance)
            self::$instance = new self();
        return self::$instance;
    }

    /**
     * Initialize global hooks.
     */
    public function init()
    {
        // Color Customize action
        add_action('wp_head', [$this, 'awp_theme_color_cus']);

        // Color Customize action
        add_action('customize_register', [$this, 'awp_slider_theme_color_cus']);

        // Add style Action
        add_action("wp_enqueue_scripts", [$this, 'awp_slider_load_css_and_js']);

        // Add Slider Custom post action
        add_action('init', [$this, 'awp_slider_custom_post_type']);

        // Add Slider ShortCode action
        add_action('init', [$this, 'awp_slider_shortcode']);

        // Add Jquery Action
        add_action('wp_footer', [$this, 'awp_slider_script_jquery'], 100);
    }


    // Including css
    function awp_slider_load_css_and_js()
    {
        // Add Slider Css
        wp_enqueue_style('awp-slider-style', (AWP_SLIDER_PLUGIN_URL . 'assets/css/awp-slider.css'), array(), AWP_PLUGIN_VERSION);
        // Add Slider jquery
        wp_enqueue_script('jquery');
        // Add Slider jquery min
        wp_enqueue_script('awp-min-slider-script', (AWP_SLIDER_PLUGIN_URL . 'assets/js/awp-slider-min.js'), array('jquery'), AWP_PLUGIN_VERSION, true);
        // Add Slider custom js
        wp_enqueue_script('awp-slider-script', (AWP_SLIDER_PLUGIN_URL . 'assets/js/awp-slider.js'), array('jquery'), AWP_PLUGIN_VERSION, true);
    }





    /**
     * Register Custom Post Type
     */

    function awp_slider_custom_post_type()
    {

        $labels = array(
            'name'                  => _x('AWP Sliders', 'Post Type General Name', 'advance-wp-slider'),
            'singular_name'         => _x('AWP Slider', 'Post Type Singular Name', 'advance-wp-slider'),
            'menu_name'             => __('AWP Sliders', 'advance-wp-slider'),
            'name_admin_bar'        => __('AWP Slider', 'advance-wp-slider'),
            'archives'              => __('Item Archives', 'advance-wp-slider'),
            'attributes'            => __('Item Attributes', 'advance-wp-slider'),
            'parent_item_colon'     => __('Parent Item:', 'advance-wp-slider'),
            'all_items'             => __('All Items', 'advance-wp-slider'),
            'add_new_item'          => __('Add New Item', 'advance-wp-slider'),
            'add_new'               => __('Add New', 'advance-wp-slider'),
            'new_item'              => __('New Item', 'advance-wp-slider'),
            'edit_item'             => __('Edit Item', 'advance-wp-slider'),
            'update_item'           => __('Update Item', 'advance-wp-slider'),
            'view_item'             => __('View Item', 'advance-wp-slider'),
            'view_items'            => __('View Items', 'advance-wp-slider'),
            'search_items'          => __('Search Item', 'advance-wp-slider'),
            'not_found'             => __('Not found', 'advance-wp-slider'),
            'not_found_in_trash'    => __('Not found in Trash', 'advance-wp-slider'),
            'featured_image'        => __('Featured Image', 'advance-wp-slider'),
            'set_featured_image'    => __('Set featured image', 'advance-wp-slider'),
            'remove_featured_image' => __('Remove featured image', 'advance-wp-slider'),
            'use_featured_image'    => __('Use as featured image', 'advance-wp-slider'),
            'insert_into_item'      => __('Insert into item', 'advance-wp-slider'),
            'uploaded_to_this_item' => __('Uploaded to this item', 'advance-wp-slider'),
            'items_list' => __('Items list', 'advance-wp-slider'),
            'items_list_navigation' => __('Items list navigation', 'advance-wp-slider'),
            'filter_items_list'     => __('Filter items list', 'advance-wp-slider'),
        );
        $args = array(
            'label'                 => __('AWP Slider', 'advance-wp-slider'),
            'description'           => __('AWP Slider Description', 'advance-wp-slider'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
        );
        register_post_type('awp-slider', $args);
    }


    /**
     * Show Slider Post Data
     */

    function awp_slider_post_loop()
    {

        ob_start();
?>
        <div id="jssor_1">
            <div class="slider" data-u="slides">
                <?php
                // WP_Query arguments
                $args = array(
                    'post_type'              => array('awp-slider'),
                    'order' => 'asc',
                );

                // The Query
                $awp_slider_query = new WP_Query($args);

                // The Loop slider item

                if ($awp_slider_query->have_posts()) {
                    while ($awp_slider_query->have_posts()) {
                        $awp_slider_query->the_post();
                        // do something
                ?>

                        <!-----------Slider item--------------->
                        <div class="slider-item">
                            <div class="slider-overlay">
                                <img data-u="image" src="<?php echo esc_url(get_the_post_thumbnail_url(get_the_ID(), 'full')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                                <div class="slider-content">
                                    <h2><?php echo esc_html(get_the_title()); ?></h2>
                                    <p><?php echo esc_html(wp_trim_words(get_the_content(), 40, '')); ?></p>
                                    <div class="btn">
                                        <a href="<?php echo esc_url(get_post_meta(get_the_ID(), 'slider_button_link', true)); ?>">
                                            <?php echo esc_html(get_post_meta(get_the_ID(), 'slider_button_text', true)); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                <?php
                    }
                } else {
                    // no posts found
                }

                // Restore original Post Data
                wp_reset_postdata();
                ?>
            </div>


            <!-- Bullet Navigator -->
            <div data-u="navigator" class="jssorb053" data-autocenter="1" data-scale="0.5" data-scale-bottom="0.75">
                <div data-u="prototype" class="i">
                    <svg viewBox="0 0 16000 16000">
                        <path class="b" d="M11400,13800H4600c-1320,0-2400-1080-2400-2400V4600c0-1320,1080-2400,2400-2400h6800 
                    c1320,0,2400,1080,2400,2400v6800C13800,12720,12720,13800,11400,13800z"></path>
                    </svg>
                </div>
            </div>
            <!-- Arrow Navigator -->
            <div data-u="arrowleft" id="arrow-left" class="jssora093" data-autocenter="2" data-scale="0.75" data-scale-left="0.75">
                <svg viewBox="0 0 16000 16000">
                    <circle class="c" cx="8000" cy="8000" r="5920"></circle>
                    <polyline class="a" points="7777.8,6080 5857.8,8000 7777.8,9920 "></polyline>
                    <line class="a" x1="10142.2" y1="8000" x2="5857.8" y2="8000"></line>
                </svg>
            </div>
            <div data-u="arrowright" id="arrow-right" class="jssora093" data-autocenter="2" data-scale="0.75" data-scale-right="0.75">
                <svg viewBox="0 0 16000 16000">
                    <circle class="c" cx="8000" cy="8000" r="5920"></circle>
                    <polyline class="a" points="8222.2,6080 10142.2,8000 8222.2,9920 "></polyline>
                    <line class="a" x1="5857.8" y1="8000" x2="10142.2" y2="8000"></line>
                </svg>
            </div>
        </div>
    <?php

        return ob_get_clean();
    }

    /*
     * Add plugin script jquery
     */

    function awp_slider_script_jquery()
    { ?>
        <script type="text/javascript">
            jssor_1_slider_init();
        </script><?php
                }


                /**
                 * Wp Slider shortcode.
                 *
                 * @return void
                 */
                function awp_slider_shortcode()
                {
                    add_shortcode('AWP-SLIDER', [$this, 'awp_slider_post_loop']);
                }


                // Add section
                // Add section
                function awp_slider_theme_color_cus($wp_customize)
                {
                    $wp_customize->add_section('awp_slider_theme_color_cus', array(
                        'title' => __('Awp Slider Customize', 'advance-wp-slider'),
                    ));

                    // Add navigator color setting
                    $wp_customize->add_setting('awp_slider_defult_color', array(
                        'default' => '#ffffff',
                        'sanitize_callback' => 'sanitize_hex_color',
                    ));
                    // Add navigator color control
                    $wp_customize->add_control(
                        new WP_Customize_Color_Control(
                            $wp_customize,
                            'awp_slider_defult_color',
                            array(
                                'label' => __('Bullet & Navigator Color', 'advance-wp-slider'),
                                'section' => 'awp_slider_theme_color_cus',
                            )
                        )
                    );

                    // Add Header color setting
                    $wp_customize->add_setting('awp_slider_header_defult_color', array(
                        'default' => '#ffffff',
                        'sanitize_callback' => 'sanitize_hex_color',
                    ));
                    // Add Header color control
                    $wp_customize->add_control(
                        new WP_Customize_Color_Control(
                            $wp_customize,
                            'awp_slider_header_defult_color',
                            array(
                                'label' => __('Header Color', 'advance-wp-slider'),
                                'section' => 'awp_slider_theme_color_cus',
                            )
                        )
                    );

                    // Add Header text size setting
                    $wp_customize->add_setting('awp_slider_header_defult_size', array(
                        'default' => '40px',
                        'sanitize_callback' => 'sanitize_text_field',
                    ));
                    // Add Header text size control
                    $wp_customize->add_control('awp_slider_header_defult_size', array(
                        'label' => __('Header Text Size', 'advance-wp-slider'),
                        'section' => 'awp_slider_theme_color_cus',
                        'type' => 'text', // Use 'text' instead of 'size'
                    ));

                    // Add title color setting
                    $wp_customize->add_setting('awp_slider_defult_title_color', array(
                        'default' => '#ffffff',
                        'sanitize_callback' => 'sanitize_hex_color',
                    ));
                    // Add title color control
                    $wp_customize->add_control(
                        new WP_Customize_Color_Control(
                            $wp_customize,
                            'awp_slider_defult_title_color',
                            array(
                                'label' => __('Title Color', 'advance-wp-slider'),
                                'section' => 'awp_slider_theme_color_cus',
                            )
                        )
                    );

                    // Add title size setting
                    $wp_customize->add_setting('awp_slider_defult_title_size', array(
                        'default' => '10px',
                        'sanitize_callback' => 'sanitize_text_field',
                    ));
                    // Add title size control
                    $wp_customize->add_control('awp_slider_defult_title_size', array(
                        'label' => __('Title Size', 'advance-wp-slider'),
                        'section' => 'awp_slider_theme_color_cus',
                        'type' => 'text', // Use 'text' instead of 'size'
                    ));

                    // Add Button size setting
                    $wp_customize->add_setting('awp_slider_defult_button_size', array(
                        'default' => '12px',
                        'sanitize_callback' => 'sanitize_text_field',
                    ));
                    // Add Button size control
                    $wp_customize->add_control('awp_slider_defult_button_size', array(
                        'label' => __('Button Size', 'advance-wp-slider'),
                        'section' => 'awp_slider_theme_color_cus',
                        'type' => 'text', // Use 'text' instead of 'size'
                    ));

                    // Add Button text color setting
                    $wp_customize->add_setting('awp_slider_defult_button_text_color', array(
                        'default' => '#ffffff',
                        'sanitize_callback' => 'sanitize_hex_color',
                    ));
                    // Add Button text color control
                    $wp_customize->add_control(
                        new WP_Customize_Color_Control(
                            $wp_customize,
                            'awp_slider_defult_button_text_color',
                            array(
                                'label' => __('Button Text Color', 'advance-wp-slider'),
                                'section' => 'awp_slider_theme_color_cus',
                            )
                        )
                    );

                    // Add Button text hover color setting
                    $wp_customize->add_setting('awp_slider_defult_button_text_hover_color', array(
                        'default' => '#ffffff',
                        'sanitize_callback' => 'sanitize_hex_color',
                    ));
                    // Add Button text hover color control
                    $wp_customize->add_control(
                        new WP_Customize_Color_Control(
                            $wp_customize,
                            'awp_slider_defult_button_text_hover_color',
                            array(
                                'label' => __('Button Text hover Color', 'advance-wp-slider'),
                                'section' => 'awp_slider_theme_color_cus',
                            )
                        )
                    );

                    //Add Button color setting
                    $wp_customize->add_setting('awp_slider_defult_button_color', array(
                        'defult' => '#000000',
                    ));
                    //Add Button color setting
                    $wp_customize->add_control('awp_slider_defult_button_color', array(
                        'label' => __('Button Color', 'advance-wp-slider'),
                        'section' => 'awp_slider_theme_color_cus',
                        'type' => 'color',
                    ));

                    //Add Button hover color setting
                    $wp_customize->add_setting('awp_slider_defult_button_hover_color', array(
                        'defult' => '#000000',
                    ));
                    //Add Button hover color setting
                    $wp_customize->add_control('awp_slider_defult_button_hover_color', array(
                        'label' => __('Button hover Color', 'advance-wp-slider'),
                        'section' => 'awp_slider_theme_color_cus',
                        'type' => 'color',
                    ));
                }


                // Theme color customize
                function awp_theme_color_cus()
                {
                    ?>

        <style>
            :root {
                --slider-navigator-color: <?php echo esc_attr(get_theme_mod('awp_slider_defult_color')); ?>;
                --slider-heading-color: <?php echo esc_attr(get_theme_mod('awp_slider_header_defult_color')); ?>;
                --slider-title-color: <?php echo esc_attr(get_theme_mod('awp_slider_defult_title_color')); ?>;
                --slider-button-text-color: <?php echo esc_attr(get_theme_mod('awp_slider_defult_button_text_color')); ?>;
                --slider-button-text-hover-color: <?php echo esc_attr(get_theme_mod('awp_slider_defult_button_text_hover_color')); ?>;
                --slider-button-color: <?php echo esc_attr(get_theme_mod('awp_slider_defult_button_color')); ?>;
                --slider-button-hover-color: <?php echo esc_attr(get_theme_mod('awp_slider_defult_button_hover_color')); ?>;

                --slider-heading-size: <?php echo esc_attr(get_theme_mod('awp_slider_header_defult_size')); ?>;
                --slider-title-size: <?php echo esc_attr(get_theme_mod('awp_slider_defult_title_size')); ?>;
                --slider-button-size: <?php echo esc_attr(get_theme_mod('awp_slider_defult_button_size')); ?>;
            }
        </style>


<?php
                }
            }

            Awp_Slider_Frontend::get_instance()->init();
