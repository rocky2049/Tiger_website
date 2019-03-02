<?php
/**
 * Single Product Meta
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );

?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php _e( 'SKU:', 'woocommerce' ); ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'woocommerce' ); ?></span>.</span>

	<?php endif; ?>

	<?php echo wc_get_product_category_list($product->get_id(), ', ', '<div class="cat-list"><label>' . _n( 'Category:', 'Categories:', $cat_count, 'woocommerce' ) . '</label> ', '</div>' ); ?>

	<?php echo wc_get_product_tag_list( $product->get_id(), ' ', '<div class="tags-list"><label>' . _n( 'Tag:', 'Tags:', $tag_count, 'woocommerce' ) . '</label> ', '</div>' ); ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>
    <div class="social-icon">
        <ul>
            <?php //var_dump($post);      
            getSocialSharing(get_the_permalink(), get_the_excerpt(), get_the_title(), 'li');?>
        </ul>
    </div>

</div>
