<?php
/**
* The template part for displaying quick access block. Including cart & search widgets
* @package athlete
*/
global $athlete_cfg,$smof_data;
if (function_exists('WC')) {
	$cartTotal = WC()->cart->cart_contents_count;
}
?>
<?php if($athlete_cfg['show-quick-access']):  ?>
    <div class="quick-access col-md-1 col-sm-2 col-xs-3">
		<div class="search-form">
            <a href="#"><i class="fa fa-search"></i></a>
            <?php get_search_form(); ?>
        </div>
        <?php if($smof_data['woocommerce_cart_top_nav'] && is_active_sidebar( 'sidebar-cart' )):  ?>
            <div class="shopping-cart">
                <a href="#"><i class="fa fa-shopping-cart"></i>
				<?php if($cartTotal){ ?>
					<span><?php echo $cartTotal ?></span>
				<?php } ?>
				</a>
                <div class="mini-cart product-popular">
                    <?php
                        dynamic_sidebar( 'sidebar-cart' );
                    ?>
                </div>
            </div>
        <?php endif ?>
    </div>
<?php endif; ?>