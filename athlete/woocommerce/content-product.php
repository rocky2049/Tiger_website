<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version 3.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product,$smof_data;

// Ensure visibility
if ( ! $product || ! $product->is_visible() ) {
	return;
}
?>
<div <?php post_class(array('product-image-wrapper')); ?>>
    <div class="product-content">
        <div class="product-image">
            <a href="<?php the_permalink(); ?>"><?php echo $product->get_image(); ?></a>
            <?php if ($product->is_on_sale()) : ?>
                <?php echo apply_filters('woocommerce_sale_flash', '<span class="onsale-label">' . __('Sale!', 'woocommerce') . '</span>', $post, $product); ?>
            <?php endif; ?>
            <?php do_action('woocommerce_showproduct_newlabel'); ?>

        </div>
        <div class="info-products">
            <div class="product-name">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>

                <div class="product-bottom"></div>
            </div>
            <div class="price-box">
                <?php echo $product->get_price_html(); ?>
            </div>
            <div class="actions">
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
    <?php if($smof_data['woocommerce_quickview']):?>
        <a href="#<?php the_ID(); ?>" class="arrows quickview">
            <i class="fa fa-arrows"></i>
            <input type="hidden" value="<?php echo esc_attr($smof_data['woocommerce_quickview_effect']);?>" />
        </a>
    <?php endif; ?>
</div>
