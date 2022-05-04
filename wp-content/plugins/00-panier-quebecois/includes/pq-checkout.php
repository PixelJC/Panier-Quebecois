<?php
if ( !defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

// --------------------------- CHECKOUT --------------------------- //

// --------- Move shipping method on checkout page ------------ //

//AJAX fragments
add_filter( 'woocommerce_update_order_review_fragments', 'my_custom_shipping_table_update' );

function my_custom_shipping_table_update( $fragments ) {
  ob_start(); ?>
  <table class="my-custom-shipping-table">
    <tbody>
      <?php wc_cart_totals_shipping_html(); ?>
    </tbody>
  </table><?php
  $woocommerce_shipping_methods = ob_get_clean();

  $fragments[ '.my-custom-shipping-table' ] = $woocommerce_shipping_methods;

  return $fragments;
}

//Custom text above shipping method table
add_action( 'woocommerce_review_order_before_shipping', 'myfct_custom_delivery_method_msg' );

function myfct_custom_delivery_method_msg() {
  if ( !myfct_return_true_if_has_category( 'entreprise' ) ) {
    echo '<h3 class="pq-delivery-selection-title">Date de livraison</h3>';
    echo '<p class="custom_orddd_delivery_method_msg"t>Entrez votre adresse complète pour voir toutes les méthodes de livraison disponibles:</p>';
  }
}

// ------- Fix delivery days in translated calendar ------- //
add_filter('wc_od_get_calendar_settings', 'pq_fix_translated_delivery_days', 10, 2);

function pq_fix_translated_delivery_days($args, $context) {

  $args['format'] = 'dd/mm/yyyy';
  return $args;
}

// ------- Move delivery date selection ------- //
add_filter( 'wc_od_checkout_location', 'pq_move_delivery_date_selection', 10, 2);

function pq_move_delivery_date_selection($location, $key ) {

  $location = array(
    'hook'     => 'woocommerce_after_checkout_billing_form',
    'priority' => 10,
  );

  return $location;
}

/**
 * Add order meta with delivery date for pickups
 */
add_action( 'woocommerce_checkout_update_order_meta', 'pq_add_pickup_date_meta', 10, 2 );

function pq_add_pickup_date_meta( $order_id, $data ) {
  if ( in_array('local_pickup_plus', $data['shipping_method']) ) {
    $pickup_date = reset($_POST['_shipping_method_pickup_date']);
    update_post_meta($order_id, '_shipping_date', $pickup_date);
    update_post_meta($order_id, '_pq_is_pickup', 'yes');
  }
}

//-------- Remove product added to cart notice -------- //
add_filter( 'wc_add_to_cart_message_html', 'myfct_remove_add_to_cart_message' );

function myfct_remove_add_to_cart_message() {
  return;
}

// -------- Move login and coupon codes on the checkout page ---------- //

//Add tips to checkout
add_action( 'woocommerce_review_order_before_payment', 'pq_tip_shortcode_fonction' );

function pq_tip_shortcode_fonction() {
  do_shortcode( '[order_tip_form]' );
}

/**
 * Replace tipping rates at checkout
 */
add_filter( 'wc_order_tip_rates', 'pq_wc_custom_order_tip_rates', 10, 1 );

function pq_wc_custom_order_tip_rates( $rates ) {
  $rates = array(
    10,
    12,
    15,
  );

  return $rates;
}

//Make custom tip string label translatable
add_filter( 'wc_order_tip_custom_label', 'pq_make_custom_tip_label_translatable' );

function pq_make_custom_tip_label_translatable( $tip_label ) {
  return esc_html__( $tip_label );
}

// ------ Login redirects to checkout if login from checkout ------ //
add_filter( 'woocommerce_login_redirect', 'myfct_redirect_checkout_login', 10, 1 );

function myfct_redirect_checkout_login( $redirect ) {
  $checkout_page_url = wc_get_checkout_url();

  if ( is_checkout() ) {
    return $checkout_page_url;
  } else {
    return $redirect;
  }
}

// ------ Minimum order amount ------ //
add_action( 'woocommerce_checkout_process', 'wc_minimum_order_amount' );
add_action( 'woocommerce_before_cart', 'wc_minimum_order_amount' );

function wc_minimum_order_amount() {
  // Set this variable to specify a minimum order value
  $minimum = 30;

  if ( WC()->cart->subtotal < $minimum ) {

    $exception_product_id = 26211;
    $cart_has_exception = false;

    foreach ( WC()->cart->get_cart() as $cart_item ) {
      $product_in_cart = $cart_item[ 'product_id' ];
      if ( $product_in_cart === $exception_product_id )$cart_has_exception = true;
    }

    if ( !$cart_has_exception ) {
      if ( is_cart() ) {

        wc_print_notice(
          sprintf(
            'Votre commande doit avoir une valeur minimum de %s',
            wc_price( $minimum )
          ),
          'error'
        );
      } else {

        wc_add_notice(
          sprintf(
            'Votre commande doit avoir une valeur minimum de %s',
            wc_price( $minimum )
          ),
          'error'
        );
      }
    }
  }
}


// --------------------------- CART --------------------------- //

// ------- Add notification text above cart ------- //
add_action( 'woocommerce_before_cart', 'myfct_cart_notification' );

function myfct_cart_notification() {
  ?>
<div id='cart-notification-wrapper'><span id='cart-notification'>
  <h4> Livraison gratuite à partir de $100 </h4>
  </span></div>
<?php
}

// -------- Change continue shopping button to link to products page -------- //
add_filter( 'woocommerce_continue_shopping_redirect', 'bbloomer_change_continue_shopping' );
add_filter( 'woocommerce_return_to_shop_redirect', 'bbloomer_change_continue_shopping' );

function bbloomer_change_continue_shopping() {
  return get_permalink( 6720 );
}

// --------------------------- THANK YOU --------------------------- //

/* ------ Add Net Promoter Score on thank you page ------ */
add_action( 'woocommerce_before_thankyou', 'pq_add_nps_to_thankyou_page', 20 );

function pq_add_nps_to_thankyou_page( $order_id ) {
  $user_id = get_current_user_id();

  if ( !empty( $user_id ) ) { ?>
    <div id="pq-thankyou-nps">
      <h2 id="my-thankyou-intro"><?php echo esc_html__('Donnez-nous votre avis!') ?></h2>
      <?php echo do_shortcode('[formidable id=2]'); ?>
    </div><?php
  }

  //Introduce rest of the thank you page
  echo '<h2 id="my-thankyou-intro">' . esc_html__( 'Votre commande:' ) . '</h2>';
}
