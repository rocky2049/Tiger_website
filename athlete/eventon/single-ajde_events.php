<?php
$oneevent = new evo_sinevent();
get_header();
?>
    <section class="page-heading">
        <div class="title-slide">
            <div class="container">
                <div class="banner-content slide-container">
                    <div class="page-title">
                        <?php if ($athlete_cfg['show-pageheading']) { ?>
                            <?php the_title('<h3>','</h3>') ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
	<?php 
	do_action('eventon_before_main_content');
	?>
    <div class="page-content">
        <?php include(get_template_directory() . '/blocks/breadcrumb.php'); ?>
        <div class="main-content our-blog">
            <div class="container">
                <div class="row">
                    <div
                        class="<?php echo ($athlete_cfg['sidebar-position'] == 'left' || $athlete_cfg['sidebar-position'] == 'right') ? 'col-lg-9 col-md-8' : 'col-md-12' ?> col-sm-12 col-xs-12 <?php echo $athlete_cfg['sidebar-position'] == 'left' ? 'pull-right' : '' ?>">
                        <?php
							$oneevent->page_content();
                        ?>
                    </div>
                    <?php if ($athlete_cfg['sidebar-position']) { ?>
                        <div
                            class="<?php echo $athlete_cfg['sidebar-position'] == 'bottom' ? 'col-md-12' : 'col-lg-3 col-md-4' ?> col-sm-12 col-xs-12 <?php echo 'pull-' . $athlete_cfg['sidebar-position'] ?>">
                            <?php get_sidebar('eventon'); ?></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php 	do_action('eventon_after_main_content'); ?>
<?php get_footer(); ?>