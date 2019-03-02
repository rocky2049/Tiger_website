<?php
/**
 * Cart Page
 *
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     3.0.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wc_print_notices();

do_action('woocommerce_before_cart');
?>
<div class="product-cart">
    <div class="cart">
        <form action="<?php echo esc_url(WC()->cart->get_cart_url()); ?>" method="post">

            <?php do_action('woocommerce_before_cart_table'); ?>

            <div class="cart-table">
                <div class="row-title">

                    <div class="col-sm-5"><span><?php _e('Product', 'woocommerce'); ?></span></div>
                    <div class="col-sm-2"><span><?php _e('Price', 'woocommerce'); ?></span></div>
                    <div class="col-sm-2"><span><?php _e('Quantity', 'woocommerce'); ?></span></div>
                    <div class="col-sm-2"><span><?php _e('Total', 'woocommerce'); ?></span></div>
                    <div class="delete-item col-sm-1"><a href="#"><i class="fa fa-times-circle"></i></a></div>
                </div>

                <?php do_action('woocommerce_before_cart_contents'); ?>

                <?php
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                        ?>
                        <div
                            class="row-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">


                            <div class="item name-item col-sm-5">
                                <?php
                                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                                if (!$_product->is_visible())
                                    echo $thumbnail;
                                else
                                    printf('<a href="%s">%s</a>', $_product->get_permalink($cart_item), $thumbnail);
                                ?>
                                <div class="product-info">
                                    <?php
                                    if (!$_product->is_visible())
                                        echo apply_filters('woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key);
                                    else
                                        echo apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', $_product->get_permalink($cart_item), $_product->get_title()), $cart_item, $cart_item_key);

                                    // Meta data
                                    echo WC()->cart->get_item_data($cart_item);

                                    // Backorder notification
                                    if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity']))
                                        echo '<p class="backorder_notification">' . __('Available on backorder', 'woocommerce') . '</p>';
                                    ?>
                                </div>
                            </div>


                            <div class="item price-item col-sm-2">
                                <span class="cart-price">
                                    <?php
                                    echo apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                                    ?>
                                </span>
                            </div>

                            <div class="item qty-item col-sm-2">
                                <?php
                                if ($_product->is_sold_individually()) {
                                    $product_quantity = sprintf('1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key);
                                } else {
                                    $product_quantity = woocommerce_quantity_input(array(
                                        'input_name' => "cart[{$cart_item_key}][qty]",
                                        'input_value' => $cart_item['quantity'],
                                        'max_value' => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                                        'min_value' => '0'
                                            ), $_product, false);
                                }

                                echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
                                ?>
                            </div>

                            <div class="item price-item col-sm-2">
                                <span class="cart-price">
                                    <?php
                                    echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                                    ?>
                                </span>
                            </div>
                            <div class="item delete-item col-sm-1">
                                <?php
                                echo apply_filters('woocommerce_cart_item_remove_link', sprintf(
                                                '<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s">&times;</a>', esc_url(WC()->cart->get_remove_url($cart_item_key)), __('Remove this item', 'woocommerce'), esc_attr($product_id), esc_attr($_product->get_sku())
                                        ), $cart_item_key);
                                ?>
                            </div>
                        </div>
                        <?php
                    }
                }

                do_action('woocommerce_cart_contents');
                ?>

                <?php if (WC()->cart->coupons_enabled()) { ?>
                    <div class="row-title">

                        <div class="col-md-12"><span><?php _e('Coupon', 'woocommerce'); ?></span></div>
                    </div>
                    <div class="row-item cart_item">
                        <div class="item col-md-12">
                            <input type="text" name="coupon_code" class="input-text" id="coupon_code" value=""
                                   placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>"/>
                            <input type="submit"
                                   class="button"
                                   name="apply_coupon"
                                   value="<?php esc_attr_e('Apply Coupon', 'woocommerce'); ?>"/>
                                   <?php do_action('woocommerce_cart_coupon'); ?>

                            <button type="submit" style="float: right;" class="button" name="update_cart" value="<?php esc_attr_e('Update Cart', 'woocommerce'); ?>"><em class="fa-icon"><i class="fa fa-refresh"></i></em> <?php _e('Update Cart', 'woocommerce'); ?></button>
                        </div>

                    </div>

                <?php } ?>

            </div>

            <div class="clearfix"></div>
            <?php do_action('woocommerce_cart_actions'); ?>

            <?php wp_nonce_field('woocommerce-cart'); ?>
            <?php do_action('woocommerce_after_cart_contents'); ?>

            <?php do_action('woocommerce_after_cart_table'); ?>

        </form>

        <div class="cart-collaterals row">
            <?php do_action('woocommerce_cart_collaterals'); ?>
        </div>

        <?php do_action('woocommerce_after_cart'); ?>
    </div>
</div>