<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop,$smof_data;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'last';
?>
<div class="col-md-12 col-sm-12 col-xs-12">
    <div class="product-image-wrapper">
        <div class="product-content">
            <div class="product-image col-md-4 col-sm-4 col-xs-12">
                <a href="<?php the_permalink(); ?>"><?php echo $product->get_image(); ?></a>
                    <?php if ( $product->is_on_sale() ) : ?>
                        <?php echo apply_filters( 'woocommerce_sale_flash', '<span class="onsale-label">' . __( 'Sale!', 'woocommerce' ) . '</span>', $post, $product ); ?>
                    <?php endif; ?>
                    <?php do_action( 'woocommerce_showproduct_newlabel' ); ?>
                <?php if($smof_data['woocommerce_quickview']):?>
                <a href="#<?php the_ID(); ?>" class="arrows quickview">
                    <i class="fa fa-arrows"></i>
                    <input type="hidden" value="<?php echo esc_attr($smof_data['woocommerce_quickview_effect']);?>" />
                </a>
                <?php endif;?>
            </div>
            <div class="info-products-list col-md-8 col-sm-8 col-xs-12">
                <div class="product-name">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </div>
                <hr>
                <div class="desc">
                    <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
                </div>
                <hr>
                <div class="product-row-bottom">
                <div class="price-box">
                    <?php echo $product->get_price_html(); ?>
                </div>
                <div class="actions-box">
                    <ul>
                        <li><a href="<?php the_permalink(); ?>"><i class="fa fa-info"></i></a></li>
                        <li><a class="add_to_cart_button product_type_simple" data-product_id="<?php echo $product->get_id(); ?>" data-product_sku="<?php echo $product->get_sku() ?>" href="<?php echo esc_url($product->add_to_cart_url())?>" data-quantity="1"><i class="fa fa-shopping-cart"></i></a>
                        </li>
                        <?php if(class_exists('YITH_WCWL')): ?>
                            <li>
                                <a href="<?php echo esc_url(YITH_WCWL()->get_wishlist_url()); ?>" rel="nofollow"
                                   data-product-id="<?php echo $product->get_id(); ?>"
                                   data-product-type="<?php echo $product->get_type(); ?>" class="link-wishlist add_to_wishlist">
                                    <i class="fa fa-star"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>