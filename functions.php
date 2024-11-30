<?php

// Defines
define( 'FL_CHILD_THEME_DIR', get_stylesheet_directory() );
define( 'FL_CHILD_THEME_URL', get_stylesheet_directory_uri() );

// Classes
require_once 'classes/class-fl-child-theme.php';

// Actions
add_action( 'wp_enqueue_scripts', 'FLChildTheme::enqueue_scripts', 1000 );

//* Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'parallax_enqueue_scripts_styles', 1000 );
function parallax_enqueue_scripts_styles() {
	// Styles
	wp_enqueue_style( 'icomoon-fonts', get_stylesheet_directory_uri() . '/icomoon.css', array() );
	wp_enqueue_style( 'custom', get_stylesheet_directory_uri() . '/style.css', array() );	
}

//Function to add Meta Tags in Header without Plugin
function add_meta_tags() {
	if ( is_page('6973') ) {
?>
	<META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=UTF-8">
	<script src="https://www.google.com/recaptcha/api.js"></script>
	<script>
	 function timestamp() { var response = document.getElementById("g-recaptcha-response"); if (response == null || response.value.trim() == "") {var elems = JSON.parse(document.getElementsByName("captcha_settings")[0].value);elems["ts"] = JSON.stringify(new Date().getTime());document.getElementsByName("captcha_settings")[0].value = JSON.stringify(elems); } } setInterval(timestamp, 500);
	</script>
<?php }}
add_action('wp_head', 'add_meta_tags');

// Function to preload LCP images on all pages - Adam 8-17-2022
add_action( 'wp_head', function(){
$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
echo '<link rel="preload" as="image" href="'.$featured_img_url.'"/>';
});

// Removes Query Strings from scripts and styles
function remove_script_version( $src ){
  if ( strpos( $src, 'uploads/bb-plugin' ) !== false || strpos( $src, 'uploads/bb-theme' ) !== false ) {
    return $src;
  }
  else {
    $parts = explode( '?ver', $src );
    return $parts[0];
  }
}
add_filter( 'script_loader_src', 'remove_script_version', 15, 1 );
add_filter( 'style_loader_src', 'remove_script_version', 15, 1 );

// Woo Image Sizes
add_filter( 'woocommerce_get_image_size_gallery_thumbnail', 'thrive_woocommerce_image_size_gallery_thumbnail', 99 );
function thrive_woocommerce_image_size_gallery_thumbnail( $size ) {
    return array(
        'width'  => 200,
        'height' => 200,
        'crop'   => 1,
    );
}

add_filter( 'woocommerce_get_image_size_single', 'thrive_woocommerce_image_size_single', 99 );
function thrive_woocommerce_image_size_single( $size ) {
    return array(
        'width'  => 620,
        'height' => 0,
        'crop'   => 1,
    );
}

// Add Additional Image Sizes
add_image_size( 'news-thumb', 260, 150, false );
add_image_size( 'news-full', 800, 300, false );
add_image_size( 'sidebar-thumb', 200, 150, false );
add_image_size( 'blog-thumb', 388, 288, true );
add_image_size( 'mailchimp', 564, 9999, false );
add_image_size( 'amp', 600, 9999, false );
add_image_size( 'home-news', 340, 187, true );
add_image_size( 'home-product', 305, 226, true );

// Gravity Forms confirmation anchor on all forms
add_filter( 'gform_confirmation_anchor', '__return_true' );

//Sets the number of revisions for all post types
add_filter( 'wp_revisions_to_keep', 'revisions_count', 10, 2 );
function revisions_count( $num, $post ) {
	$num = 3;
    return $num;
}

// Enable Featured Images in RSS Feed and apply Custom image size so it doesn't generate large images in emails
function featuredtoRSS($content) {
global $post;
if ( has_post_thumbnail( $post->ID ) ){
$content = '<div>' . get_the_post_thumbnail( $post->ID, 'mailchimp', array( 'style' => 'margin-bottom: 15px;' ) ) . '</div>' . $content;
}
return $content;
}
 
add_filter('the_excerpt_rss', 'featuredtoRSS');
add_filter('the_content_feed', 'featuredtoRSS');

//****** AMP Customizations ******/

// Add Fav Icon to AMP Pages
add_action('amp_post_template_head','amp_favicon');
function amp_favicon() { ?>
	<link rel="icon" href="<?php echo get_site_icon_url(); ?>" />
<?php } 

// Add Banner below content of AMP Pages
add_action('ampforwp_after_post_content','amp_custom_banner_extension_insert_banner');
function amp_custom_banner_extension_insert_banner() { ?>
	<div class="amp-custom-banner-after-post">
		<h2>IF YOU HAVE ANY QUESTIONS, PLEASE DO NOT HESITATE TO CONTACT US</h2>
		<a class="ampforwp-comment-button" href="/contact-us">
			CONTACT US
		</a>
	</div>
<?php }

// Rename the Default Sorting Options
add_filter( 'woocommerce_catalog_orderby', 'thrive_rename_default_sorting_options' );

function thrive_rename_default_sorting_options( $options ){

	unset( $options[ 'popularity' ] ); // remove
	$options[ 'popularity' ] = 'Popularity'; // rename
	
	unset( $options[ 'date' ] ); // remove
	$options[ 'date' ] = 'Latest'; // rename
	
	unset( $options[ 'price' ] ); // remove
	$options[ 'price' ] = 'Price: Low to High'; // rename
	
	unset( $options[ 'price-desc' ] ); // remove
	$options[ 'price-desc' ] = 'Price: High to Low'; // rename

	return $options;

}

//Remove Gutenberg Block Library CSS from loading on the frontend
function smartwp_remove_wp_block_library_css(){
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    //wp_dequeue_style( 'wc-block-style' ); // Remove WooCommerce block CSS
} 
add_action( 'wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100 );

add_action( 'wp_enqueue_scripts', function() {
    wp_dequeue_style( 'font-awesome' ); // FontAwesome 4
    wp_enqueue_style( 'font-awesome-5' ); // FontAwesome 5

    wp_dequeue_style( 'jquery-magnificpopup' );
    wp_dequeue_script( 'jquery-magnificpopup' );

    wp_dequeue_script( 'bootstrap' );
    wp_dequeue_script( 'jquery-fitvids' );
    wp_dequeue_script( 'jquery-waypoints' );
}, 9999 );

/* Site Optimization - Removing several assets from Home page that we dont need */

// Remove Assets from HOME page only
function remove_home_assets() {
  if (is_front_page()) {
      
	  wp_dequeue_style('yoast-seo-adminbar');
	  wp_dequeue_style('addtoany');
	  wp_dequeue_style('pwb-styles-frontend');
	  wp_dequeue_style('wc-blocks-vendors-style');
	  wp_dequeue_style('wc-blocks-style');
	  wp_dequeue_style('woocommerce-layout');
	  wp_dequeue_style('woocommerce-smallscreen');
	  wp_dequeue_style('woocommerce-general');
	  wp_dequeue_style('tinvwl-webfont-font');
	  wp_dequeue_style('tinvwl-webfont');
	  wp_dequeue_style('tinvwl');
	  wp_dequeue_style('rvpplugin-slick');
	  wp_dequeue_style('rvpplugin-slick-theme');
	  wp_dequeue_style('rvpplugin-frontend');
	  wp_dequeue_style('prdctfltr');
	  wp_dequeue_style('font-awesome-5');
	  wp_dequeue_style('font-awesome');
	  
	  wp_dequeue_script('addtoany-core');
	  wp_dequeue_script('addtoany-jquery');
	  wp_dequeue_script('pwb-functions-frontend');
	  wp_dequeue_script('tinvwl');
	  wp_dequeue_script('rvpplugin-slick');
	  wp_dequeue_script('rvpplugin-frontend');
	  
  }
  
};
add_action( 'wp_enqueue_scripts', 'remove_home_assets', 9999 );

// Woocommerce
add_action( 'wp_enqueue_scripts', 'child_manage_woocommerce_styles', 99 );
function child_manage_woocommerce_styles() {
 //remove generator meta tag
 remove_action( 'wp_head', array( $GLOBALS['woocommerce'], 'generator' ) );
 
 //first check that woo exists to prevent fatal errors
 if ( function_exists( 'is_woocommerce' ) ) {
 //dequeue scripts and styles
 if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
	wp_dequeue_script( 'wc-add-to-cart' );
 	wp_dequeue_script( 'wc-cart-fragments' );
	
	 wp_dequeue_style( 'woocommerce_frontend_styles' );
	 wp_dequeue_style( 'woocommerce_fancybox_styles' );
	 wp_dequeue_style( 'woocommerce_chosen_styles' );
	 wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
	 wp_dequeue_script( 'wc_price_slider' );
	 wp_dequeue_script( 'wc-single-product' );
	 wp_dequeue_script( 'wc-add-to-cart' );
	 wp_dequeue_script( 'wc-cart-fragments' );
	 wp_dequeue_script( 'wc-checkout' );
	 wp_dequeue_script( 'wc-add-to-cart-variation' );
	 wp_dequeue_script( 'wc-single-product' );
	 wp_dequeue_script( 'wc-cart' );
	 wp_dequeue_script( 'wc-chosen' );
	 wp_dequeue_script( 'woocommerce' );
	 wp_dequeue_script( 'prettyPhoto' );
	 wp_dequeue_script( 'prettyPhoto-init' );
	 wp_dequeue_script( 'jquery-blockui' );
	 wp_dequeue_script( 'jquery-placeholder' );
	 wp_dequeue_script( 'fancybox' );
	 wp_dequeue_script( 'jqueryui' );
	
 }
 }
 
}


//Removing unused Default Wordpress Emoji Script - Performance Enhancer
function disable_emoji_dequeue_script() {
    wp_dequeue_script( 'emoji' );
}
add_action( 'wp_print_scripts', 'disable_emoji_dequeue_script', 100 );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 ); 
remove_action( 'wp_print_styles', 'print_emoji_styles' );

// Removes Emoji Scripts 
add_action('init', 'remheadlink');
function remheadlink() {
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'feed_links', 2);
	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);
	remove_action('wp_head', 'start_post_rel_link', 10, 0);
	remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);
	remove_action('wp_head', 'wp_shortlink_header', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
}

// Removes woo zoom and lightbox
add_action( 'after_setup_theme', 'cg_remove_zoom_lightbox_theme_support', 99 );
function cg_remove_zoom_lightbox_theme_support() {
remove_theme_support( 'wc-product-gallery-zoom' );
remove_theme_support( 'wc-product-gallery-lightbox' );
}

// Woocomerce SKU Uniqueness
add_filter( 'wc_product_has_unique_sku', '__return_false' );

// Woocomerce Remove first Option
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'filter_dropdown_option_html', 12, 2 );
function filter_dropdown_option_html( $html, $args ) {
    $show_option_none_text = $args['show_option_none'] ? $args['show_option_none'] : __( 'Choose an option', 'woocommerce' );
    $show_option_none_html = '<option value="">' . esc_html( $show_option_none_text ) . '</option>';

    $html = str_replace($show_option_none_html, '', $html);

    return $html;
}

// Always Show Variation Price
add_action('woocommerce_before_add_to_cart_form', 'selected_variation_price_replace_variable_price_range');
function selected_variation_price_replace_variable_price_range(){
    global $product;

    if( $product->is_type('variable') ):
    ?><style> .woocommerce-variation-price {display:none;} </style>
    <script>
    jQuery(function($) {
        var p = 'p.price'
            q = $(p).html();

        $('form.cart').on('show_variation', function( event, data ) {
            if ( data.price_html ) {
                $(p).html(data.price_html);
            }
        }).on('hide_variation', function( event ) {
            $(p).html(q);
        });
    });
    </script>
    <?php
    endif;
}

// Returns product price based on sales
$price = get_post_meta( get_the_ID(), '_regular_price', true);
   $price_sale = get_post_meta( get_the_ID(), '_sale_price', true);
                if ($price_sale !== "") {
                    echo $price_sale;
                } else {
                    echo $price;
                }

// Woocomerce Products Saving
add_filter( 'woocommerce_get_price_html', 'modify_woocommerce_get_price_html', 10, 2 );

function modify_woocommerce_get_price_html( $price, $product ) {

    if( $product->is_on_sale() && ! is_admin() ){
		$pricePer = number_format(($product->regular_price - $product->sale_price) / $product->regular_price * 100, 2).'%';
		$savePrice = $product->regular_price - $product->sale_price;
		$savePrice = get_woocommerce_currency_symbol() . $savePrice;
        return $price . sprintf( __('<div class="saving-details"><span class="save-price">You Save: %s</span><span class="save-per"> (%s)</span></div>', 'woocommerce' ), $savePrice, $pricePer );
	}
	else{
        return $price;
	}
}


// Brand
add_shortcode('getCurrProdBrand', function (){
	$brand = wp_get_post_terms( get_the_ID(), 'pwb-brand' );
	return '<div class="brand-name">'.$brand[0]->name.'</div>';
});


// Product Tabs
add_filter( 'woocommerce_product_tabs', 'my_remove_all_product_tabs', 10 , 1 );
 
function my_remove_all_product_tabs( $tabs ) {
	$id = get_the_ID();
	$checkSpec = get_field('specification', $id);
	$checkBene = get_field('benefits_features', $id);
	$checkProdDetails = have_rows('product_details' );
	if(!empty($checkProdDetails))
	  $tabs['product_details_tab'] = array(
		  'title' => __( 'Product Details', 'woocommerce' ),
		  'priority' => 10,
		  'callback'  => 'product_details_tab_content');
	if(!empty($checkBene))
		$tabs['benefits_features_tab'] = array(
			'title' => __( 'Benefits & Features', 'woocommerce' ),
			'priority' => 20,
			'callback'  => 'benefits_features_tab_content');
	if(!empty($checkSpec))		
		$tabs['specification_tab'] = array(
			'title' => __( 'Specification', 'woocommerce' ),
			'priority' => 30,
			'callback'  => 'specification_tab_content');
  unset( $tabs['description'] );        // Remove the description tab
  unset( $tabs['pwb_tab'] );       // Remove the reviews tab
  unset( $tabs['additional_information'] );    // Remove the additional information tab
  $tabs['reviews']['priority'] = 40;  
  return $tabs;
}
function specification_tab_content(){
//	var_dump(get_the_ID());
	$id = get_the_ID();
	echo get_field('specification', $id);

}
function benefits_features_tab_content(){
	$id = get_the_ID();
	echo get_field('benefits_features', $id);

}
function product_details_tab_content(){
	$id = get_the_ID();
	// Check rows exists.
$i = 0;
$totalRows = count(get_field('product_details'));
if( have_rows('product_details') ):

		// Loop through rows.
		while( have_rows('product_details') ) : the_row();
			$i++;
			// Load sub field value.
			$title = get_sub_field('detail_heading');
			$details = get_sub_field('details');
			// Do something...
			// style="'.(!empty($details) && $i == 1 ? 'width:100%' : '').'" 
			$html = '<div class="product_col col_'.$i.'" style="width:calc(100%/'.$totalRows.');"  >';
				$html .= '<h5>'.$title.'</h5>';
				$html .= '<div class="content">'.$details.'</div>';
			$html .= '</div>';
			echo $html;
		// End loop.
		endwhile;

	// No value.
	else :
		// Do something...
	endif;
	// echo get_field('product_details', $id);

}
