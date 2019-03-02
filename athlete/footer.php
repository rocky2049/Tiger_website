<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package athlete
 */
global $athlete_cfg,$smof_data;
?>
</div> <!--end .content-wrapper -->
</section><!--end section.page -->
<?php
if ( $smof_data['footer_widgets'] &&  is_active_sidebar( $athlete_cfg['footer-option']?'sidebar-footer2':'sidebar-footer1' ) ):
?>
<footer class="page-footer <?php if($athlete_cfg['footer-option']) echo 'footer-store' ?>">
		<section>
			<div class="container">
				<div class="row">
                        <?php  dynamic_sidebar($athlete_cfg['footer-option']?'sidebar-footer2':'sidebar-footer1');?>
				</div>
			</div>
		</section>
</footer>
<?php endif;?>
<?php if($smof_data['backtotop-button']): ?>
<div id="copyright" <?php if($athlete_cfg['footer-option']) echo 'class="copyright-store"' ?>>
	<div class="container">
		<div class="back-to-top"><a href="#top" title="Back to top"><i class="fa fa-chevron-up"></i></a></div>
		<div class="clrDiv"></div>
	</div>
</div>
<?php endif; ?>
</div>
<!-- .wrapper -->
<?php wp_footer(); ?>
</body>
</html>
