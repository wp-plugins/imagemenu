<?php

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

global $imagemenu_opts;


function imagemenu_init_opts() {
    $css = '#imagemenu {width:100%}

#imagemenu ul {margin:0;
height:200px;
text-align:center;
width:100%;}

#imagemenu li {
display:inline-block;
width:200px;
margin:1px;
position:relative;
}

.imagemenu-title {
font-size:1.5em;
font-family:"sans-serif";
color:#fff;
position:absolute;
top:150px;
left:0px;
padding:3px;
background: rgb(69, 60, 55); /* fallback color */
background: rgba(69, 60, 55, 0.7);
z-index:99; /* make sure it displays above the image */
}

#imagemenu a {
display:block;
height:200px;
text-decoration:none;
overflow:hidden;
}
#imagemenu img{
top: 0px;
left: 0px;
position:relative;
}';
    imagemenu_set_option_default('maincss', $css);

    $css = '/* Specify individual imgs that should be moved. Use a negative to move up or left.*/
#imagemenu-%page_slug% img {
top: 0px;
left: 0px;
position:relative;
}';
    imagemenu_set_option_default('imagecss',$css);
    imagemenu_set_option_default('thumbnail_size','medium');
}

/**
 * call imagemenu_init_opts here to get default options set
 *  even if not using the plugin right away.
 * Makes sure that this is up-to-date for the admin page
 *
 */
imagemenu_init_opts();

function imagemenu_set_option_default($option_name, $default) {
	global $imagemenu_opts;
	
	$imagemenu_opts[$option_name] = get_option($option_name);
	if ($imagemenu_opts[$option_name] == '' ) {
		$imagemenu_opts[$option_name] = $default;

		update_option($option_name, $imagemenu_opts[$option_name]);
	}
}

/**
 * =========================================================
 * Add options to admin menu
 */

add_action('admin_menu', 'imagemenu_menu'); // ok for 2.9

function imagemenu_menu() {
	add_options_page('ImageMenu Options', 'ImageMenu', 'manage_options', 'imagemenu_options_identifier' , 'imagemenu_options', 'favicon.ico');
	add_action( 'admin_init', 'register_im_settings' );
}

function register_im_settings() {
	//register our settings
	
	add_settings_section('ims', __('Style Settings','imagemenu'), 'im_section_text', 'imsec');

        register_setting( 'ims', 'thumbnail_size' );
	add_settings_field('thumbnail_size', __('Default size of image (small, medium, large or size in pixels i.e. 245x245) if specifying the size in pixels the longest side will be set.','imagemenu') , 'thumbnail_size_input', 'imsec', 'ims');

        register_setting( 'ims', 'maincss','im_sanitize_maincss' );
	add_settings_field('maincss', __('Main CSS style to be applied. This is only output once when menu is genereated.','imagemenu') , 'maincss_textarea', 'imsec', 'ims');

        register_setting( 'ims', 'imagecss','im_sanitize_maincss' );
	add_settings_field('imagecss', __('Style that can be applied to individual &lt;li&gt; elements. Use %page_slug% for targetting all &lt;li&gt; elements, and %image_src% for the location of the particular image src.  This CSS will be output for EVERY &lt;li&gt; created','imagemenu') , 'imagecss_textarea', 'imsec', 'ims');
}

// category section

function im_section_text()
{
	echo '<p>'.__('CSS style to be applied to the ImageMenu','imagemenu').'</p>';
}	


function maincss_textarea() {
	// Style not selected
	echo '<textarea rows="10" cols="60" name="maincss" >' . html_entity_decode(get_option('maincss')) . '</textarea>';
}
function imagecss_textarea() {
	// Style not selected
	echo '<textarea rows="10" cols="60" name="imagecss" >' . html_entity_decode(get_option('imagecss')) . '</textarea>';
}
function thumbnail_size_input(){
    echo '<input type="text" size="20" name="thumbnail_size" value="'.get_option('thumbnail_size').'" />';
}
function im_sanitize_maincss($data){
    //var_dump($data);
    return htmlentities($data);
}

function imagemenu_options() {
	?>
	<div class="wrap">
	<h2>imagemenu</h2>
	<p>
		<?php echo __('See full notes at', 'imagemenu') ; ?>
		<a href="http://timhodson.com/imagemenu/">http://timhodson.com/imagemenu/</a>
		<form action="https://www.paypal.com/cgi-bin/webscr" method="post"> <input name="cmd" type="hidden" value="_s-xclick" /> <input name="hosted_button_id" type="hidden" value="6104650" /> <input alt="PayPal - The safer, easier way to pay online." name="submit" src="https://www.paypal.com/en_GB/i/btn/btn_donate_LG.gif" type="image" /> <img src="https://www.paypal.com/en_GB/i/scr/pixel.gif" border="0" alt="" width="1" height="1" /></form>
	</p>
	
	<form method="post" action="options.php">

	<?php 
	settings_fields( 'ims' );
	do_settings_sections('imsec');
	?>

	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
	</p>

	</form>

        <p>
            The folowing layout is used for the HTML in the menu.
        <blockquote>
            <pre>
                &lt;div id="imagemenu" class="imagemenu"&gt;
                &lt;ul&gt;
                    &lt;li id="imagemenu-%page_slug%"&gt;
                        &lt;span class=".imagemenu-title"&gt;Title Here&lt;/span&gt;
                        &lt;img src="" alt="" title=""/&gt;
                    &lt;/li&gt;
                    ...
                &lt;/ul&gt;
                &lt;/div&gt;
                
            </pre>
        </blockquote>
        </p>

	</div>
	
	<?php
}

?>