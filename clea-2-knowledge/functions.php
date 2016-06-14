<?php
/**
 * 
 * this file is designed to provide specific functions for the child theme
 *
 * @package    clea-2-mairie
 * @subpackage Functions
 * @version    1.0
 * @since      0.1.0
 * @author     Anne-Laure Delpech <ald.kerity@gmail.com>  
 * @copyright  Copyright (c) 2015 Anne-Laure Delpech
 * @link       
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */


// Do theme setup on the 'after_setup_theme' hook.
add_action( 'after_setup_theme', 'clea_mairie_theme_setup', 11 ); 

// Remove cleaner-gallery css. Necessary for jetpack gallery.
add_action( 'wp_enqueue_scripts', 'clea_mairie_remove_cleaner_gallery', 99 );

# Change Read More link in automatic Excerpts
remove_filter('get_the_excerpt', 'wp_trim_excerpt');
add_filter('get_the_excerpt', 'wpse_custom_wp_trim_excerpt');


function clea_mairie_theme_setup() {

	// Get the child template directory and make sure it has a trailing slash.
	$child_dir = trailingslashit( get_stylesheet_directory() );
	// var_dump( $child_dir );
	// require_once( $child_dir . 'inc/setup1.php' );

	/* Register and load scripts. */
	add_action( 'wp_enqueue_scripts', 'clea_mairie_enqueue_scripts' );

	/* Register and load styles. */
	add_action( 'wp_enqueue_scripts', 'clea_knowledge_enqueue_styles', 4 ); 

	/* Set content width. */
	hybrid_set_content_width( 700 );	
	
	// add theme support for WordPress featured image and post thumbnails
	add_theme_support( 'featured-header' );
	add_theme_support( 'post-thumbnails' ); 

	// Add support for the Site Logo plugin and the site logo functionality in JetPack
	// https://github.com/automattic/site-logo
	// http://jetpack.me/
	add_theme_support( 'site-logo', array( 'size' => 'medium' ) );
	
	// see http://themehybrid.com/board/topics/custom-header-extended-with-custom-child-theme-of-stargazer
	add_filter( 'jetpack_photon_override_image_downsize', '__return_true' );

	/* override stargazer custom header sizes */
	add_theme_support(

		'custom-header',

		array(

			'default-image'          => '%s/images/headers/orange-burn.jpg',

			'random-default'         => false,

			'width'                  => 1600,

			'height'                 => 400,

			'flex-width'             => true,

			'flex-height'            => true,

			'default-text-color'     => '252525',

			'header-text'            => true,

			'uploads'                => true,

			'wp-head-callback'       => 'stargazer_custom_header_wp_head'

		)

	);
	
	
}
 

function clea_mairie_remove_cleaner_gallery() {
	// necessary if using jetpack gallery
	// source http://themehybrid.com/board/topics/loads-gallery-min-css-twice
	wp_dequeue_style( 'gallery' );	
}


	
 
function clea_knowledge_enqueue_styles() {

	// feuille de style pour l'impression
	wp_enqueue_style( 'print', get_stylesheet_directory_uri() . '/css/print.css', array(), false, 'print' );

	// feuille de style pour le co-marquage service public.fr
	wp_enqueue_style( 'co-marquage', get_stylesheet_directory_uri() . '/css/co-marquage.css', array(), false, 'all' );

	/*
	* enqueue font awesome 4.0 from CDN
	* @since  1.0.0
	*/
	wp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css' );
}

function clea_mairie_enqueue_scripts() {

	
	if ( is_page_template( 'page/cb-front-page-test1.php' ) ) {
		wp_enqueue_script( 'jquery-masonry' );
	}
}

/*******************************************
* Change Read More link in Excerpts 
*
* see 
* http://wordpress.stackexchange.com/questions/207050/read-more-tag-shows-up-on-every-post
* http://wordpress.stackexchange.com/questions/141125/allow-html-in-excerpt/141136#141136
*  

*******************************************/

function wpse_allowedtags() {
    // Add custom tags to this string
	// <a>,<img>,<video>,<script>,<style>,<audio> are not in
    return '<br>,<em>,<i>,<ul>,<ol>,<li>,<p>'; 
}

if ( ! function_exists( 'wpse_custom_wp_trim_excerpt' ) ) : 

    function wpse_custom_wp_trim_excerpt($wpse_excerpt) {
		$raw_excerpt = $wpse_excerpt;
		
		// text for the "read more" link
		$rm_text = __( 'La suite &raquo;', 'stargazer' ) ;
		$excerpt_end = ' <a class="more-link" href="'. esc_url( get_permalink() ) . '">' . $rm_text . '</a>'; 
		
		
        if ( '' == $wpse_excerpt ) {  

            $wpse_excerpt = get_the_content('');
            $wpse_excerpt = strip_shortcodes( $wpse_excerpt );
            $wpse_excerpt = apply_filters('the_content', $wpse_excerpt);
            $wpse_excerpt = str_replace(']]>', ']]&gt;', $wpse_excerpt);
            $wpse_excerpt = strip_tags($wpse_excerpt, wpse_allowedtags()); /*IF you need to allow just certain tags. Delete if all tags are allowed */

            //Set the excerpt word count and only break after sentence is complete.
                $excerpt_word_count = 75;
                $excerpt_length = apply_filters('excerpt_length', $excerpt_word_count); 
                $tokens = array();
                $excerptOutput = '';
                $count = 0;

                // Divide the string into tokens; HTML tags, or words, followed by any whitespace
                preg_match_all('/(<[^>]+>|[^<>\s]+)\s*/u', $wpse_excerpt, $tokens);

                foreach ($tokens[0] as $token) { 

                    if ($count >= $excerpt_length && preg_match('/[\,\;\?\.\!]\s*$/uS', $token)) { 
                    // Limit reached, continue until , ; ? . or ! occur at the end
                        $excerptOutput .= trim($token);
                        break;
                    }

                    // Add words to complete sentence
                    $count++;

                    // Append what's left of the token
                    $excerptOutput .= $token;
                }

            $wpse_excerpt = trim(force_balance_tags($excerptOutput));
		   
				// $wpse_excerpt .= $excerpt_end ;
				$excerpt_more = apply_filters( 'excerpt_more', ' ' . $excerpt_end ); 

                $pos = strrpos($wpse_excerpt, '</');
                if ($pos !== false) {
					// Inside last HTML tag
					$wpse_excerpt = substr_replace($wpse_excerpt, $excerpt_end, $pos, 0); // Add read more next to last word 
				} else {
					// After the content
					$wpse_excerpt .= $excerpt_more; //Add read more in new paragraph 
				}
                
            return $wpse_excerpt;   

        } /* else {
			return 'AAA ! ' . $raw_excerpt;
		} */
		
		// add read more link to the manual extract
		$wpse_excerpt .= $excerpt_end ;
		// return the manual extract
        // return apply_filters('wpse_custom_wp_trim_excerpt', 'AAA ! ' . $wpse_excerpt, $raw_excerpt);
		return apply_filters('wpse_custom_wp_trim_excerpt', $wpse_excerpt, $raw_excerpt);
    }
  
endif; 


?>