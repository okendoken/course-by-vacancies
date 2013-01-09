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
add_filter( 'page_template', 'wpa3396_page_template' );
function wpa3396_page_template( $page_template )
{
    if ( is_page() and 1 == get_post_meta(get_the_ID(), '_vc_page_template', true)) {
        $page_template = dirname( __FILE__ ) . '/custom-page-template.php';
    }
    return $page_template;
}


add_action( 'add_meta_boxes', 'myplugin_add_custom_box' );

/* Do something with the data entered */
add_action( 'save_post', 'myplugin_save_postdata' );

/* Adds a box to the main column on the Post and Page edit screens */
function myplugin_add_custom_box() {
    add_meta_box(
        'myplugin_sectionid',
        __( 'My Post Section Title', 'myplugin_textdomain' ),
        'myplugin_inner_custom_box',
        'page'
    );
}

/* Prints the box content */
function myplugin_inner_custom_box( $post ) {

    // Use nonce for verification
    wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename' );

    // The actual fields for data entry
    // Use get_post_meta to retrieve an existing value from the database and use the value for the form
    $value = get_post_meta( get_the_ID(), $key = '_vc_page_template', $single = true );
    echo '<label for="myplugin_new_field">';
    _e("Description for this field", 'myplugin_textdomain' );
    echo '</label> ';
    echo '<input type="text" id="myplugin_new_field" name="myplugin_new_field" value="'.$value.'" size="25" />';
}

/* When the post is saved, saves our custom data */
function myplugin_save_postdata( $post_id ) {
    // verify if this is an auto save routine.
    // If it is our form has not been submitted, so we dont want to do anything
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
        return;

    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times

    if ( !wp_verify_nonce( $_POST['myplugin_noncename'], plugin_basename( __FILE__ ) ) )
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
    $mydata = sanitize_text_field( $_POST['myplugin_new_field'] );

    // Do something with $mydata
    // either using
    add_post_meta($post_ID, '_vc_page_template', $mydata, true) or
        update_post_meta($post_ID, '_vc_page_template', $mydata);
    // or a custom table (see Further Reading section below)
}