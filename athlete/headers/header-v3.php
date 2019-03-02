<?php
global $athlete_cfg,$smof_data;
?>
<body id="page-top" <?php body_class(); ?> data-offset="90" data-target=".navigation" data-spy="scroll">
<div class="wrapper hide-main-content">
    <section class="page cms-home header-option boxing-page <?php echo esc_attr($athlete_cfg['page-classes']);?>">
        <?php get_template_part('blocks/menu-mobile'); ?>
        <div class="content-wrapper">
<!--Header-->

<header id="header" class="header header-container header-container-4 alt reveal">
    <div class="container">
        <div class="row">
            <div class="col-md-2 col-sm-4 col-xs-4 logo">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_url($smof_data['logo']) ?>" alt="Logo"/></a>
            </div>
            <div class="col-md-9 nav-container">
                <?php get_template_part('blocks/menu-desktop'); ?>
            </div>
            <?php get_template_part('blocks/quick-access'); ?>
            <button class="menu-button" id="open-button"></button>
        </div>
    </div>
</header>
<!--End Header-->