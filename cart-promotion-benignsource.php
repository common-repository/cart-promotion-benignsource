<?php
/**
 * Plugin Name: Cart Promotion BenignSource
 * Plugin URI: http://www.benignsource.com/
 * Description: Promote Special Product or Accessories for each product in Shopping Cart
 * Author: BenignSource
 * Author URI: http://www.benignsource.com/
 * Version: 1.0
 * Tested up to: 5.9
 */

defined( 'ABSPATH' ) or exit;

// Check if WooCommerce is active and bail if it's not
if ( ! WooCommerceCartPromotionBenignSource::is_woocommerce_activecpbs() ) {
	return;
}

class WooCommerceCartPromotionBenignSource {

	private $cpbs_cart_promotion = false;
	

	/** plugin version number */
	const VERSION = '1.0';

	/** @var WooCommerceCartPromotionBenignSource single bscartpromotion of this plugin */
	protected static $bscartpromotion;
	

	/** plugin version name */
	const VERSION_OPTION_NAME = 'woocommerce_bscartpromotion_db_version';


	/*
	 * WooCommerce is known to be active and initialized
	 */
	public function __construct() {
		// Installation
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) $this->install();

		add_action( 'init',             array( $this, 'load_translation' ) );
		add_action( 'woocommerce_init', array( $this, 'init' ) );
	}


	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 */
	public function __clone() {

		/* translators: Placeholders: %s - plugin name */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot clone instances of %s.', 'cart-promotion-benignsource' ), 'Cart Promotion BenignSource' ), '1.0' );
	}


	/*
	 * Unserializing instances is forbidden due to singleton pattern.
	 */
	public function __wakeup() {

		/* translators: Placeholders: %s - plugin name */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot unserialize instances of %s.', 'cart-promotion-benignsource' ), 'Cart Promotion BenignSource' ), '1.0' );
	}



	/**
	 * Init WooCommerce Cart Promotion extension once we know WooCommerce is active
	 */
	public function init() {
		// backend stuff
		add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'cart_promotion_write_panel_tab' ) );
		add_action( 'woocommerce_product_write_panels',     array( $this, 'cart_promotion_write_panel' ) );
		add_action( 'woocommerce_process_product_meta',     array( $this, 'cart_promotion_save_data' ), 10, 2 );
		

		// frontend stuff
		add_action( 'woocommerce_before_cart', 'cart_promotion_benignsource' );
	   
	  
function cart_promotion_benignsource() {

global $woocommerce;
$items = $woocommerce->cart->get_cart();
$ids = array();
foreach($items as $item => $values) { 
        $cpbsproduct = $values['data']->post; 
        $ids[] = $cpbsproduct->ID; 
}
$cpbs_cart_promotion = maybe_unserialize( get_post_meta( $cpbsproduct->ID, 'cpbs_woo_cart_promotion', true ) );

foreach ( $cpbs_cart_promotion as $cartpromotion ){
$productcpbs = new WC_Product($cartpromotion['productid']);
$cpbsproductsid = $cartpromotion['productid'];
if ($cpbsproductsid  == null ){
} else{
echo '<!--Cart Promotion BenignSource-->';
echo '<div style="padding:10px;background:#e96656; font-size:24px;color:#FFFFFF; width:100%;border-radius: 5px;"><div style="width:50%; display:inline-block;"><i>' . $cartpromotion['titlecart'] . '</i></div><div style="float:right; display:inline-block;"><a href="' . $cartpromotion['specialoffers'] . '" style="background:#fff; color:#e96656;border-radius: 5px; padding:5px; font-size:16px;">See More Offers</a></div></div>';
echo '<div style="padding:10px;padding-top:0px;border:5px #e96656 solid;margin-bottom:10px; border-radius: 5px;">';

echo '<div style="padding:20px; width:20%; display:inline-block;font-size:24px;color:#e96656;"><b>' . $cartpromotion['cart'] . '</b></div>';
echo '<div style="padding:10px; width:40%; display:inline-block;">';
echo '<div style="width:40%; display:inline-block;">';
echo '<a href="' . get_permalink( $cartpromotion['productid'] ) . '" title="' . get_the_title($cartpromotion['productid']) . '">';
        echo get_the_post_thumbnail( $cartpromotion['productid'], 'thumbnail', array( 'style' => 'border-radius: 50%;border: 4px #e96656 solid;' )  );
        echo '</a></div>';
echo '<div style="width:60%; display:inline-block;">';
echo '<div style="width:100%;font-size:18px;padding:5px;"><i>' . get_the_title($cartpromotion['productid']) . '</i></div>';
echo '<div style="width:60%;font-size:18px;padding:5px;color:#e96656;"><b>Price '. get_woocommerce_currency_symbol(). ' ' . $productcpbs->get_price() . '</b></div>';
echo '<div style="width:60%;"><a rel="nofollow" href="?post_type=product&#038;add-to-cart=' . $cartpromotion['productid'] . '" data-quantity="1" data-product_id="' . $cartpromotion['productid'] . '" data-product_sku="" class="button product_type_simple add_to_cart_button ajax_add_to_cart">Add to cart</a></div>';
echo '</div></div>';
echo '</div>';
echo '<!--Cart Promotion BenignSource-->';
}
  }	
}
}
	/**
	 * Adds Cart Promotion Data postbox in the admin product interface
	 */
	public function cart_promotion_write_panel_tab() {
		echo "<li class=\"cart_promotion_tab\"><a href=\"#woocommerce_cart_promotion_benignsource\">" . __( 'Cart Promotion', 'woocommerce_cart_promotion' ) . "</a></li>";
	
	}

	/**
	 * Adds Cart Promotion Data postbox in the product interface
	 */
	public function cart_promotion_write_panel() {
		global $post;
		// the product

		// pull the data out of the database
		$cpbs_cart_promotion = maybe_unserialize( get_post_meta( $post->ID, 'cpbs_woo_cart_promotion', true ) );
		

		if ( empty( $cpbs_cart_promotion ) ) {
			$cpbs_cart_promotion[] = array( 'titlecart' => '', 'cart' => '', 'productid' => '' );
		}

foreach ( $cpbs_cart_promotion as $cartpromotion ) {
echo '<div id="woocommerce_cart_promotion_benignsource" class="panel wc-metaboxes-wrapper woocommerce_options_panel">';
?>
<ul class="tabcartbs">
<li style="color:#FFFFFF; padding:10px; font-size:18px;">Cart Promotion</li>
<li style="float:right;"><?php echo '<img src="' . esc_attr( plugins_url( 'logo_cart.jpg', __FILE__ ) ) . '" alt="Cart Promotion BenignSource" border="0px"> ';?></li></ul>
<?php 
echo '<div style="padding:15px;">';
echo '<div style="padding:15px;border-bottom:1px #e96656 solid;"><i>Promote Special Offer or Accessories for this product</i></div>';
        woocommerce_wp_text_input( array( 'id' => '_cpbs_cart_promotion_title', 'label' => __( 'Name of Promotion', 'woocommerce_cart_promotion' ), 'placeholder' => __( 'Get Special Offers Now!', 'woocommerce_cart_promotion' ), 'value' => $cartpromotion['titlecart'], 'style' => 'width:70%;' ) );
		woocommerce_wp_text_input( array( 'id' => '_cpbs_cart_promotion_cart', 'label' => __( 'Describe ', 'woocommerce_cart_promotion' ), 'placeholder' => __( 'You Save $27.00 
Of Regular Price!', 'woocommerce_cart_promotion' ), 'value' => $cartpromotion['cart'], 'style' => 'width:70%;' ) );
		woocommerce_wp_text_input( array( 'id' => '_cpbs_cart_promotion_productid', 'label' => __( 'Product ID', 'woocommerce_cart_promotion' ), 'placeholder' => __( 'Enter Product ID', 'woocommerce_cart_promotion' ), 'value' => $cartpromotion['productid'], 'style' => 'width:70%;' ) );
		woocommerce_wp_text_input( array( 'id' => '_cpbs_cart_promotion_specialoffers', 'label' => __( 'Special Offers', 'woocommerce_cart_promotion' ), 'placeholder' => __( 'Premium Version', 'woocommerce_cart_promotion' ), 'value' => $cartpromotion['specialoffers'], 'style' => 'width:70%;' ) );
	
	
echo '<div style="">';
echo '<div style="padding:15px;border-bottom:1px #e96656 solid;"><i>Promote Other Product or Accessories for this product</i></div>';
       
echo '<div style=" text-align:center; font-size:16px; color:#e96656; padding:10px;">This is Free version if you like it support us and take <a href="http://www.benignsource.com/product/cart-promotion-benignsource/" target="_blank" title="Premium Version">Premium Version</a></div></div>';
		

echo '<div style="">';
echo '<div style="padding:15px;border-bottom:1px #e96656 solid;"><i>Select Background Color</i></div>';			

	    echo '<div style=" text-align:center; font-size:16px; color:#e96656; padding:10px;">This is Free version if you like it support us and take <a href="http://www.benignsource.com/product/cart-promotion-benignsource/" target="_blank" title="Premium Version">Premium Version</a></div></div>';		
		echo '</div></div>';
		}
	}
	/*
	 * Saves the data inputed into the product boxes, as post meta data
	 */
	public function cart_promotion_save_data( $post_id, $post ) {

		$cartpromotion_title = stripslashes( $_POST['_cpbs_cart_promotion_title'] );
		$cartpromotion_cart = stripslashes( $_POST['_cpbs_cart_promotion_cart'] );
		$cartpromotion_productid = stripslashes( $_POST['_cpbs_cart_promotion_productid'] );
		
		
			// save the data to the database
			$cpbs_cart_promotion[] = array( 'titlecart' => $cartpromotion_title, 'id' => $cartpromotion_id, 'cart' => $cartpromotion_cart,'productid' => $cartpromotion_productid );

update_post_meta( $post_id, 'cpbs_woo_cart_promotion', $cpbs_cart_promotion );	
		
	}
	
	/*
	 * @return WooCommerceCartPromotionBenignSource
	 */
	public static function bscartpromotion() {
		if ( is_null( self::$bscartpromotion ) ) {
			self::$bscartpromotion = new self();
		}
		return self::$bscartpromotion;
	}
	/*
	 * Checks if WooCommerce is active
	 */
	public static function is_woocommerce_activecpbs() {

		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}
	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 */
	private function install() {

		global $wpdb;

		$installed_version = get_option( self::VERSION_OPTION_NAME );

		// installed version lower than plugin version?
		if ( -1 === version_compare( $installed_version, self::VERSION ) ) {
			// new version number
			update_option( self::VERSION_OPTION_NAME, self::VERSION );
		}
	}

}


/*
 * @return \WooCommerceCartPromotionBenignSource
 */
function cpbs_cart_promotion_benign() {
	return WooCommerceCartPromotionBenignSource::bscartpromotion();
}

function load_cart_promotion_admin_style() {
       
        wp_enqueue_style( 'cart_promotion_admin_css', plugins_url('style.css', __FILE__) );
		
}
add_action( 'admin_enqueue_scripts', 'load_cart_promotion_admin_style' );
/**
 * The WooCommerceCartPromotionBenignSource global object
 */
$GLOBALS['woocommerce_cart_promotion_benignsource'] = cpbs_cart_promotion_benign();
