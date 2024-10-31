<?php
/**
 * Plugin Name: Random Images
 * Description: Display a set of random attached images with the [random_images] shortcode.
 * Version: 1.0
 * Author: Sheri Bigelow
 * Author URI: http://designsimply.com/
 * License: GPLv2 or later
 */

class Random_Images_Plugin {

        static function load() {
                add_shortcode( 'random_images', array( 'Random_Images_Plugin', 'random_images' ) );
        }

        static function random_images( $attr ) {
                $attr = shortcode_atts( array(
                        'size' => 'thumbnail',
                        'link' => '',
                        'total' => 6,
                ), $attr );

	        // Query the database for just the image ids.
	        // TODO add escaped limit input
	        global $wpdb;

	        $sql = "
	        SELECT
	                ID
	        FROM
	                $wpdb->posts
	        WHERE
	                post_type='attachment'
	                AND post_mime_type LIKE 'image%'
	                AND post_status='inherit'
	        ORDER BY RAND() LIMIT " . $attr['total'];

	        $image_ids = $wpdb->get_results( $sql );
	        shuffle( $image_ids );

	        foreach ( $image_ids as $image ) {
	                $my_images[] = array(
	                        'title' => get_the_title( $image->ID ),
	                        'url' => wp_get_attachment_url( $image->ID ),
	                        'image' => wp_get_attachment_image( $image->ID, $attr['size'] )
	                );
	        }

	        if ( ! empty( $my_images ) ) {
	                $output = '<div class="random-images">';
	                foreach ($my_images as $my_image) {
	                        // TODO sanitize the output
	                        $output .= ' <a href="' . $my_image['url'] . '" title="' . $my_image['title'] . '">' . $my_image['image'] . '</a>';
	                }
	                $output .= '</div><!-- #random-images -->';
					return $output;
	        } else {
	                if ( is_user_logged_in() ) {
	                        return "Error: no images found. Add some images to posts.";
	                }
	        }
        }
}
Random_Images_Plugin::load();
