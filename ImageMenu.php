<?php
/*
Plugin Name: ImageMenu
Plugin URI: http://timhodson.com/imageMenu/
Description: Create an image menu using featured images
Version: 0.2
Author: Tim Hodson
Author URI: http://timhodson.com
Text Domain: imagemenu
*/
/*  Copyright 2009  Tim Hodson  (email : tim@timhodson.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
 * If nothing works with images then you need to put this in your theme's functions.php
 *  add_theme_support( 'post-thumbnails' );
 */


if ( ! defined( 'IM_VERSION' ) )
    define( 'IM_VERSION', '0.2' );

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
    define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
    define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
    define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
    define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

$plugin_dir = basename(dirname(__FILE__));
load_plugin_textdomain( 'imagemenu', WP_PLUGIN_DIR.$plugin_dir, $plugin_dir.'/languages' );

include_once( WP_PLUGIN_DIR.'/'.$plugin_dir."/options.php" );

add_shortcode('imagemenu', 'imagemenu_func');
add_shortcode('im', 'imagemenu_func');

function imagemenu_init() {
    wp_enqueue_script('jQuery');
}

add_action('init', 'imagemenu_init');


function imagemenu_func($atts){
    global $imagemenu_opts;
    // get pages from shortcode
    // page_slugs in the order in which they are to be shown
    // get height and width from options


    extract(shortcode_atts(array (
            'page_slugs' => '',
            'thumbnail_size' => $imagemenu_opts['thumbnail_size']
            ), $atts));

    if(strstr($thumbnail_size,'x'))
    {
        $imagemenu_opts['thumbnail_size'] = split('x',$thumbnail_size);
    }
    else
    {
        $imagemenu_opts['thumbnail_size'] = $thumbnail_size;
    }


    $slugs = explode(',', $page_slugs);
    foreach ($slugs as $v) {
        $pages[] = get_page_by_path($v);
    }

    //start building HTML
    $out = '<div id="imagemenu" class="imagemenu"> <!-- ImageMenu Version: '.IM_VERSION.' --> <ul>';
    $out .= '<style type="text/css">';
    $out .= html_entity_decode($imagemenu_opts['maincss']);
    $out .= '</style>';

    $out .= '<script type="text/javascript">';
    $out .= '
$j = jQuery.noConflict();
  $j(window).load( function () {
                    $j(".imagemenu-title").fadeOut(5000);
                }
            )
        
            ';

    $out .= '</script>';

    foreach ($pages as $page){
    // for each page slug get the page details

        // title
        $title = $page->post_title ;

        // link
        $link = get_page_link($page->ID);

        // image
        if(function_exists('get_the_post_thumbnail')){
            $img = get_the_post_thumbnail($page->ID, $imagemenu_opts['thumbnail_size'] );
             
            $src = preg_replace('/^.*src=["](.*\.[a-zA-Z]{3})["].*$/', '${1}', $img);  //TODO regex may not yet be foolproof
            //$src = preg_replace('/^.*src=["]([\-\_\:\/\.a-zA-Z]*)["].*$/', '${1}', $img);  //TODO regex may not yet be foolproof
        }else{
             $img = '';
        }
        //TODO, allow individual images to be styled using the slug
        $style = html_entity_decode($imagemenu_opts['imagecss']);
        $style = str_replace("%page_slug%", $page->post_name, $style );
        $style = str_replace("%image_src%", $src, $style);

       // var_dump($page);

        $out .= '<style type="text/css"> '.$style.' </style>';
        
      
        // output the li
        $out .= '<li id="imagemenu-'.$page->post_name .'"><a href="'.$link.'" alt="'.$title.'"><span class="imagemenu-title imagemenu-title-'.$page->post_name.'" >'.$title.'</span>'.$img.'</a>' ;
          // jquery
        $out.= '<script type="text/javascript">
            $j("#imagemenu-'.$page->post_name.'").hover(
                function(){
                    //alert("HERE");
                    $j(".imagemenu-title-'.$page->post_name.'").fadeIn(200);
                    }
                ,
                function(){
                    $j(".imagemenu-title-'.$page->post_name.'").fadeOut(500);
                    }
                )
                </script>';
        $out .= '</li>';

    }

    
    $out .= '</ul></div>';
//    <div id="imagemenu" class="imagemenu">
//    <ul>
//        <li id="imagemenu-%page_slug%">
//            <span class=".imagemenu-title">Title Here</span>
//            <img src="" alt="" title=""/>
//        </li>
//    </ul>
//    </div>

    echo $out ;

}

?>