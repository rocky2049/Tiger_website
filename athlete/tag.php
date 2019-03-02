<?php
/**
 * The template for displaying Tag pages
 *
 * Used to display archive-type pages for posts in a tag.
 * @package athlete
 */

get_header();
?>
<section class="page-heading">
    <div class="title-slide">
        <div class="container">
            <div class="banner-content slide-container">
                <div class="page-title">
                    <?php if($athlete_cfg['show-pageheading']){ ?>
                        <h3><?php echo single_tag_title() ?></h3>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="page-content">
    <?php get_template_part('blocks/breadcrumb'); ?>
    <div class="main-content our-blog">
        <div class="container">
            <div class="row">
                <div class="<?php echo ($athlete_cfg['sidebar-position'] == 'left' || $athlete_cfg['sidebar-position'] == 'right') ? 'col-lg-9 col-md-8' : 'col-md-12' ?> col-sm-12 col-xs-12 <?php echo $athlete_cfg['sidebar-position'] == 'left' ? 'pull-right' : '' ?>">
                    <?php if ( have_posts() ) : ?>
                        <?php while (have_posts()) : the_post(); ?>
                            <?php
                            $post_format = get_post_format();
                            switch ($post_format) {
                                case 'link':
                                    $p_format = 'link';
                                    break;
                                case 'video':
                                    $p_format = 'video';
                                    break;
                                case 'quote':
                                    $p_format = 'quote';
                                    break;
                                case 'gallery':
                                    $p_format = 'gallery';
                                    break;
                                default:
                                    $p_format = 'atdefault';
                                    break;
                            }
                            get_template_part('content', $p_format); ?>
                        <?php endwhile; // end of the loop. ?>
                        <?php get_template_part('blocks/paging'); ?>
                    <?php else :
                        // If no content, include the "No posts found" template.
                        get_template_part( 'content', 'none' );
                    endif;?>
                </div>
                <?php if ($athlete_cfg['sidebar-position']) { ?>
                    <div class="<?php echo $athlete_cfg['sidebar-position'] == 'bottom' ? 'col-md-12' : 'col-lg-3 col-md-4' ?> col-sm-12 col-xs-12 <?php echo 'pull-' . $athlete_cfg['sidebar-position'] ?>">
                        <?php get_sidebar($athlete_cfg['sidebar-name']); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
