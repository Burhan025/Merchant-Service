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

// Show Minimum Price on Shop Page
add_filter('woocommerce_variable_price_html', 'custom_variation_price', 10, 2);
function custom_variation_price( $price, $product ) {
$available_variations = $product->get_available_variations();
$selectedPrice = ”;
$dump = ”;

foreach ( $available_variations as $variation )
{
// $dump = $dump . ” . var_export($variation[‘attributes’], true) . ”;

$isDefVariation=false;
foreach($product->get_default_attributes() as $key=>$val){
// $dump = $dump . ” . var_export($key, true) . ”;
// $dump = $dump . ” . var_export($val, true) . ”;
if($variation['attributes']['attribute_'.$key]==$val){
$isDefVariation=true;
}
}
if($isDefVariation){
$price = $variation['display_price'];
}
}
$selectedPrice = wc_price($price);

// $dump = $dump . ” . var_export($available_variations, true) . ”;

return $selectedPrice . $dump;
}


// Shop page 2nd function
add_filter('woocommerce_variable_price_html', 'custom_variation_price', 10, 2); 

function custom_variation_price( $price, $product ) { 

     $price = '';

     $price .= wc_price($product->get_price()); 

     return $price;
}

--
	
	function njengah_regularsale_price_at_cart( $old_display, $cart_item, $cart_item_key ) {
    $product = $cart_item['data'];
    if ( $product ) {
        return $product->get_price_html();
    }
    return $old_display;
     }
    add_filter( 'woocommerce_cart_item_price', 'njengah_regularsale_price_at_cart', 10, 3 );

--
	
	// Saving
add_filter( 'woocommerce_get_price_html', 'modify_woocommerce_get_price_html', 10, 2 );

function modify_woocommerce_get_price_html( $price, $product ) {
    if( $product->is_on_sale() && ! is_admin() )
        return $price . sprintf( __('<p>Save %s</p>', 'woocommerce' ), $product->regular_price - $product->sale_price );
    else
        return $price;
}