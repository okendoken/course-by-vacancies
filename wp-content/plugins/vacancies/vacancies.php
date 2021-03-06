<?php
/**
 * @package Vacancies
 */
/*
Plugin Name: Vacancies
Plugin URI: http://localhost/
Description: Used by millions
Version: 0.1
Author: Okendoken
Author URI: http://okendoken.com/
License: GPLv2 or later
*/
include 'guide.php';
include 'theme_options.php';
include 'lib/metabox.php';
include 'lib/post-types.php';
include 'lib/blog-widget.php';
include 'lib/job-widget.php';

add_filter( 'page_template', 'vc_page_template' );
function vc_page_template( $page_template )
{
    if (is_page()){
        $page_template_meta = get_post_meta(get_the_ID(), '_vc_page_template', true);
        if (1 == $page_template_meta) {
            $page_template = dirname( __FILE__ ) . '/index-jobs.php';
        } else if (2 == $page_template_meta) {
            $page_template = dirname( __FILE__ ) . '/jobform.php';
        }
    }
    return $page_template;
}

add_filter( 'single_template', 'vc_post_template' );
function vc_post_template( $post_template )
{
    if (is_single() and get_post_type() == 'job'){
        $post_template = dirname( __FILE__ ).'/single-job.php';
    }
    return $post_template;
}

add_filter( 'archive_template', 'vc_archive_template' );
function vc_archive_template( $archive_template )
{
    if ( get_post_type() == 'job' ) {
        $archive_template = dirname( __FILE__ ) . '/index-jobs.php';
    }
    return $archive_template;
}


add_action( 'add_meta_boxes', 'vc_add_custom_box' );

/* Do something with the data entered */
add_action( 'save_post', 'vc_save_postdata' );

/* Adds a box to the main column on the Post and Page edit screens */
function vc_add_custom_box() {
    add_meta_box(
        'myplugin_sectionid',
        __( 'My Post Section Title', 'myplugin_textdomain' ),
        'vc_inner_custom_box',
        'page'
    );
}

/* Prints the box content */
function vc_inner_custom_box( $post ) {

    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'vc_noncename' );

    // The actual fields for data entry
    // Use get_post_meta to retrieve an existing value from the database and use the value for the form
    $value = get_post_meta( get_the_ID(), $key = '_vc_page_template', $single = true );
    echo '<label for="vc_new_field">';
    _e("Description for this field", 'myplugin_textdomain' );
    echo '</label> ';
    echo '<input type="text" id="vc_new_field" name="vc_new_field" value="'.$value.'" size="25" />';
}

/* When the post is saved, saves our custom data */
function vc_save_postdata( $post_id ) {
    // verify if this is an auto save routine.
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times

    if ( !wp_verify_nonce( $_POST['vc_noncename'], plugin_basename( __FILE__ ) ) )
        return;


    // Check permissions
    if ( 'page' == $_POST['post_type'] )
    {
        if ( !current_user_can( 'edit_page', $post_id ) )
            return;
    }
    else
    {
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }

    // OK, we're authenticated: we need to find and save the data

    //if saving in a custom table, get post_ID
    $post_ID = $_POST['post_ID'];
    //sanitize user input
    $mydata = sanitize_text_field( $_POST['vc_new_field'] );

    // Do something with $mydata
    // either using
    add_post_meta($post_ID, '_vc_page_template', $mydata, true) or
        update_post_meta($post_ID, '_vc_page_template', $mydata);
    // or a custom table (see Further Reading section below)
}

add_filter('wp_head', 'vc_add_styles');
function vc_add_styles(){
    wp_enqueue_style('my-style',plugins_url( 'style.css' , __FILE__));
}

/* FEATURED THUMBNAILS */

if ( function_exists( 'add_theme_support' ) ) { // Added in 2.9
    add_theme_support( 'post-thumbnails' );
}

/* SHORT TITLES */

function short_title($after = '', $length) {
    $mytitle = explode(' ', get_the_title(), $length);
    if (count($mytitle)>=$length) {
        array_pop($mytitle);
        $mytitle = implode(" ",$mytitle). $after;
    } else {
        $mytitle = implode(" ",$mytitle);
    }
    return $mytitle;
}

/* PAGE NAVIGATION */


function getpagenavi(){
    ?>
<div id="navigation" class="clearfix">
    <?php if(function_exists('wp_pagenavi')) : ?>
    <?php wp_pagenavi() ?>
    <?php else : ?>
    <div class="alignleft"><?php next_posts_link(__('&laquo; Older Entries','web2feeel')) ?></div>
    <div class="alignright"><?php previous_posts_link(__('Newer Entries &raquo;','web2feel')) ?></div>
    <div class="clear"></div>
    <?php endif; ?>

</div>

<?php
}