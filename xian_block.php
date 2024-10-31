<?php

/*
Plugin Name: Repeated Blocks
Plugin URI: https://www.kohbeixian.com/wp-plugins/repeated-blocks/
Description: Used to create sections that will repeat throughout the website. Supports Visual Composer. If adding shortcode via Visual Composer: 1) Make sure 'Full Width Row' and 'Full Width Content' are turned on. 2) Add a row in the page, add Text Block and put in the shortcode. Usage: [xian_block section="section-name"]
Version: 1.0
Author: Xian
Author URI: http://kohbeixian.com
*/


// add repeated blocks

function xb_create_blocks() {

    register_post_type( 'xb-blocks',
        // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Blocks' ),
                'singular_name' => __( 'Block' )
            ),
            'public' => true,
            'has_archive' => false,
            'rewrite' => array('slug' => 'xb-blocks'),
            'menu_icon' => 'dashicons-feedback'
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'xb_create_blocks' );



//case studies Short-code
add_shortcode('xian_block', 'xb_xian_block');

function xb_xian_block($atts){

    $attr = shortcode_atts(
        array(
            'section'   => 'contact-footer',
        ),
        $atts
    );


    ob_start();

    $args = array(
        'posts_per_page'   => 1,
        'post_type'        => 'xb-blocks',
        'name'             => $attr['section']
    );

    // $testimonials = get_posts($args);
    $block = new WP_Query($args);


    while ($block->have_posts()): $block->the_post();
//        $post = get_post(get_the_ID());
        $blockContent = get_post_field('post_content', get_the_ID());

        echo do_shortcode($blockContent);

    endwhile;

    wp_reset_query();
    ?>



    <?php
    return ob_get_clean();
}

//add_action( 'init', 'xb_block_buttons' );
function xb_block_buttons() {
    add_filter( "mce_external_plugins", "xb_block_add_buttons" );
    add_filter( 'mce_buttons', 'xb_block_register_buttons' );
}
function xb_block_add_buttons( $plugin_array ) {
    $plugin_array['xian_block'] = plugin_dir_url( __FILE__ ) . 'xian_block.js';
    return $plugin_array;
}
function xb_block_register_buttons( $buttons ) {
    array_push( $buttons, 'add_block' ); // add blocks
    return $buttons;
}

new XB_Shortcode_Tinymce();
class XB_Shortcode_Tinymce
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'xb_shortcode_button'));
        add_action('admin_footer', array($this, 'xb_get_shortcodes'));
    }

    /**
     * Create a shortcode button for tinymce
     *
     * @return [type] [description]
     */
    public function xb_shortcode_button()
    {
        if( current_user_can('edit_posts') &&  current_user_can('edit_pages') )
        {
            add_filter( 'mce_external_plugins', array($this, 'xb_add_buttons' ));
            add_filter( 'mce_buttons', array($this, 'xb_register_buttons' ));
        }
    }

    /**
     * Add new Javascript to the plugin scrippt array
     *
     * @param  Array $plugin_array - Array of scripts
     *
     * @return Array
     */
    public function xb_add_buttons( $plugin_array )
    {
        $plugin_array['xb_addblockshortcodes'] = plugin_dir_url( __FILE__ ) . 'xian_block.js';

        return $plugin_array;
    }

    /**
     * Add new button to tinymce
     *
     * @param  Array $buttons - Array of buttons
     *
     * @return Array
     */
    public function xb_register_buttons( $buttons )
    {
        array_push( $buttons, 'separator', 'xb_addblockshortcodes' );
        return $buttons;
    }

    /**
     * Add shortcode JS to the page
     *
     * @return HTML
     */
    public function xb_get_shortcodes()
    {

        $args = array(
            'post_type' => 'xb-blocks',
            'post_status' => 'publish'
        );

        $blocks = new WP_Query($args);

        echo '<script type="text/javascript">var shortcodes_button = new Array();';

        $count = 0;

        if ($blocks->have_posts()):
            while ($blocks->have_posts()): $blocks->the_post();
                $title = get_the_title();
                $slug = basename(get_permalink());
                echo "shortcodes_button[{$count}] = '{$slug}';";
                $count++;
            endwhile;
        endif;

        echo '</script>';

    }
}