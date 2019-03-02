<!--Menu desktop-->
<?php
global $athlete_cfg,$smof_data;

if(!has_nav_menu($athlete_cfg['theme-menu'])){
    return;
}
?>
<nav class="megamenu collapse navbar-collapse bs-navbar-collapse navbar-right mainnav col-md-10" role="navigation">
    <?php wp_nav_menu(array(
        "container"         => "",
        'menu' => $athlete_cfg['theme-menu-id'],
		'theme_location'  => $athlete_cfg['theme-menu'],
        "menu_class"         => "nav-menu",
        "walker"            => new Inwave_Nav_Walker(),
    )); ?>
</nav>