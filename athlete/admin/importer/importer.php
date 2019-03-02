<?php
/**
 * inhost import demo data content
 *
 * @package inhost
 */
defined('ABSPATH') or die('You cannot access this script directly');
// Sample Data Importer

// Hook importer into admin init
add_action('wp_ajax_inwave_importer', 'inwave_importer');

function inwave_importer()
{
    WP_Filesystem();
    if (!defined('WP_LOAD_IMPORTERS')) define('WP_LOAD_IMPORTERS', true); // we are loading importers

    if (!class_exists('WP_Importer')) { // if main importer class doesn't exist
        include ABSPATH . 'wp-admin/includes/class-wp-importer.php';
    }
    include get_template_directory() . '/admin/importer/wordpress-importer.php';

    if (!current_user_can('manage_options')) {
        inwave_import_response('error', 'Error: Permission denied');
    }

    if(!headers_sent()) {
        if (!session_id()) {
            session_start();
        }
    }else {
        inwave_import_response('error', 'Error: Could not start session! Please try to turn off debug mode and error reporting');
    }
    $importer = array();


    if(isset($_REQUEST['iw_stage']) && $_REQUEST['iw_stage']=='init') {
        if(isset($_REQUEST['iw_data_type'])){
            $importer['steps'] = $_REQUEST['iw_data_type'];
        }else{
            $importer['steps'] = array();
        }
        if(isset($_REQUEST['iw_post_type'])){
            array_unshift($importer['steps'],'post');
        }
        array_unshift($importer['steps'],'prepare');
    }else{
        $importer = unserialize($_SESSION['inwave_importer']);
        $importer['base']->message = '';
    }

    ob_start();
    $step = $importer['steps'][0];

    $code ='continue';
    $among = 3;
    //$post_type = array('attachment','nav_menu_item','iw_portfolio', 'page','post', 'product_variation', 'product');
    switch($step){
        case 'prepare':
            /* Prepare data */

            $importer['base'] = new WP_Import_Extend();
            $importer['base']->fetch_attachments = true;
            $importer['base']->step_total = $among;
            //$importer['base']->debug = true;
            $iw_post_types= array();
            if(isset($_REQUEST['iw_post_type'])){
                $iw_post_types = $_REQUEST['iw_post_type'];
            }
			$importer['base']->exclude_post_title[] = 'Calendar Full View';
			if(!in_array('event',$iw_post_types)) {
				$importer['base']->exclude_post_title[] = 'Event Calendar';
				$importer['base']->exclude_post_title[] = 'Event Listing';
				$importer['base']->exclude_post_title[] = 'Event Details';
			}
            /** include post & term type */
            foreach ($iw_post_types as $post_type) {
                switch ($post_type) {
                    case 'post':
                        $importer['base']->allow_post_types[] = 'post';
                        $importer['base']->step_total += $among;
                        break;
                    case 'page':
                        array_push($importer['steps'], 'page');
                        $importer['base']->allow_post_types[] = 'page';
                        $importer['base']->step_total += $among;
                        break;
                    case 'woocommerce':
                        //update option before importing
                        update_option('shop_thumbnail_image_size',array('width'=>230,'height'=>230,'crop'=>1));
                        update_option('yith_wcmg_zoom_position', 'inside');
                        $importer['base']->allow_post_types[] = 'product_variation';
                        $importer['base']->allow_post_types[] = 'product';
                        $importer['base']->allow_term_types[] = 'product_tag';
                        $importer['base']->allow_term_types[] = 'product_type';
                        $importer['base']->allow_term_types[] = 'product_cat';
                        array_push($importer['steps'], 'woocommerce');
                        $importer['base']->step_total += $among;
                        break;
                    case 'event':
						if(!in_array('post',$iw_post_types)) {
							$importer['base']->allow_post_types[] = 'post';
							$importer['base']->step_total += $among;
						}
                        $importer['base']->allow_post_types[] = 'ajde_events';
                        $importer['base']->allow_term_types[] = 'event_type';
                        $importer['base']->allow_term_types[] = 'event_location';
                        $importer['base']->allow_term_types[] = 'event_organizer';
                        $importer['base']->allow_term_types[] = 'event_type_2';
                        array_push($importer['steps'], 'event');
                        $importer['base']->step_total += $among;
                        break;
                    case 'course':
						if(!in_array('post',$iw_post_types)) {
							$importer['base']->allow_post_types[] = 'post';
							$importer['base']->step_total += $among;
						}
                        $importer['base']->allow_post_types[] = 'iw_teacher';
                        $importer['base']->allow_post_types[] = 'iw_courses';
                        $importer['base']->allow_term_types[] = 'iw_courses_class';
                        array_push($importer['steps'], 'course');
                        $importer['base']->step_total += $among;
                        break;
                    case 'menu':
                        $importer['base']->allow_post_types[] = 'nav_menu_item';
                        $importer['base']->allow_term_types[] = 'nav_menu';
                        array_push($importer['steps'], 'menu');
                        $importer['base']->step_total += $among;
                        break;
                    case 'media':
                        $importer['base']->allow_post_types[] = 'attachment';
                        $importer['base']->step_total += $among;
                        break;
                }
            }


            /** Exclude media type */
            $data_types = inwave_get_import_data_types();
            foreach($data_types['post-type'] as $data_key => $data_value){
                if(!in_array($data_key,$iw_post_types)){
                    $importer['base']->exclude_attachment_types[] = $data_key;
                }
            }
            if(!in_array('slider',$importer['steps'])){
                $importer['base']->exclude_attachment_types[] = 'slider';
            }
            if(!in_array('ap-background',$importer['steps'])){
                $importer['base']->exclude_attachment_types[] = 'ap-background';
            }

            /** push finishing step */
            array_push($importer['steps'],'finish');
            $importer['base']->step_total += $among;
			
			/** get data from xml */
			$theme_xml = get_template_directory() . '/admin/importer/data/main_data.xml';
			$importer['base']->import_start($theme_xml);
			

            /** Done preparing step */
            array_shift($importer['steps']);
            $importer['base']->step_done = $among;
            $message = 'Prepared data successfully';

            break;
        case 'page':
            inwave_import_pages();
            array_shift($importer['steps']);
            $importer['base']->step_done += $among;
            $message = 'Imported pages successfully';
            break;
        case 'post':
            if(!$importer['base']->importing()){
                array_shift($importer['steps']);
                $message = 'Imported post data successfully';
            }else{
                $message = $importer['base']->message;
                $importer['base']->step_done ++;
            }
            break;
        case 'slider':
            inwave_import_sliders();
            array_shift($importer['steps']);
            $importer['base']->step_done += $among;
            $message = 'Imported sliders successfully';
            break;
        case 'ap-background':
            inwave_import_ap_backround();
            array_shift($importer['steps']);
            $importer['base']->step_done += $among;
            $message = 'Imported AP Backround Galleries successfully';
            break;
        case 'event':
            inwave_import_event();
            array_shift($importer['steps']);
            $importer['base']->step_done += $among;
            $message = 'Imported Events successfully';
            break;
        case 'woocommerce':
            inwave_import_woocommerce();
            array_shift($importer['steps']);
            $importer['base']->step_done += $among;
            $message = 'Imported woocommerce successfully';
            break;
        case 'course':
            inwave_import_course();
            array_shift($importer['steps']);
            $importer['base']->step_done += $among;
            $message = 'Imported courses successfully';
            break;
        case 'widget':
            inwave_import_widgets();
            array_shift($importer['steps']);
            $importer['base']->step_done += $among;
            $message = 'Imported widgets successfully';
            break;
        case 'menu':
            inwave_import_menu();
            array_shift($importer['steps']);
            $importer['base']->step_done += $among;
            $message = 'Imported menu successfully';
            break;
        case 'finish':

            global $wpdb;
            //update wrong url in metadata:
            $wpdb->query($wpdb->prepare("update wp_postmeta set meta_value = replace(meta_value,'http://inwavethemes.com/demo-images/athlete/wp-content','%s')",content_url()));

            $importer['base']->import_end();
            $importer['base']->step_done = $importer['base']->step_total;
            if(!count($importer['base']->error_msg)) {
                $message = '<b style="color:#444">Cheers! The demo data has been imported successfully! Please reload this page to finish!</b>';
            }else{
                $message = '<b style="color:#444">Data import completed!</b><br />'.'<div>'.implode('<br />',$importer['base']->error_msg).'</div>';
            }
            $code ='completed';
            break;
        default:
            inwave_import_response('error', 'Error: step not found: '. $step);
            break;
    }

    ob_end_clean();
    /** store state to session */
    $_SESSION['inwave_importer'] = serialize($importer);

    // calculate processed percent
    $percent = round(($importer['base']->step_done/$importer['base']->step_total)*100);
	if($percent > 100) $percent = 100;

    /** response to client */
    inwave_import_response($code, $message,$percent);
}

function inwave_get_import_data_types(){
    $data_types = array();
    $data_types['post-type'] = array();
    $data_types['data-type'] = array();

    $data_types['post-type']['page'] = 'Pages';
    $data_types['post-type']['post'] = 'Posts';

    if (class_exists('coursesBox')) {
        $data_types['post-type']['course'] = 'Courses';
    }

    if (class_exists('Woocommerce')) {
        $data_types['post-type']['woocommerce'] = 'Woocommerce';
    }
    if (class_exists( 'EventON' ) ) {
        $data_types['post-type']['event'] = 'Events';
    }
    $data_types['post-type']['media'] = 'Media';
    $data_types['post-type']['menu'] = 'Menus';
    $data_types['data-type']['widget'] = 'Widgets';

    if (class_exists('btParallaxBackgroundUtility')) {
        $data_types['data-type']['ap-background'] = 'AP Background';
    }

    if (class_exists('MSP_Importer')) {
        $data_types['data-type']['slider'] = 'Sliders';
    }
    return $data_types;
}
function inwave_import_pages(){
    global $wp_filesystem;
    // Set reading options
    $homepage = get_page_by_title( 'Homepage' );
    if($homepage->ID) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $homepage->ID); // Front Page
    }

    /** Import Theme Options */
    $theme_options_txt = get_template_directory() . '/admin/importer/data/theme_options.txt'; // theme options data file
    $theme_options_txt = $wp_filesystem->get_contents($theme_options_txt);
    $data = unserialize(base64_decode($theme_options_txt));  /* decode theme options data */

    of_save_options($data); // update theme options

}

function inwave_import_menu(){

    // Set imported menus to registered theme locations
    $locations = get_theme_mod( 'nav_menu_locations' ); // registered menu locations in theme
    $menus = wp_get_nav_menus(); // registered menus

    if($menus) {
        foreach($menus as $menu) { // assign menus to theme locations
            if( $menu->name == 'Main Menu' ) {
                $locations['primary'] = $menu->term_id;
            }
            if( $menu->name == 'OnePage' ) {
                $locations['onepage'] = $menu->term_id;
            }
        }
    }

    set_theme_mod( 'nav_menu_locations', $locations ); // set menus to locations

}
function inwave_import_woocommerce(){

    // Set pages
    $woopages = array(
        'woocommerce_shop_page_id' => 'Shop',
        'woocommerce_cart_page_id' => 'Cart',
        'woocommerce_checkout_page_id' => 'Checkout',
        'woocommerce_pay_page_id' => 'Checkout &#8594; Pay',
        'woocommerce_thanks_page_id' => 'Order Received',
        'woocommerce_myaccount_page_id' => 'My Account',
        'woocommerce_edit_address_page_id' => 'Edit My Address',
        'woocommerce_view_order_page_id' => 'View Order',
        'woocommerce_change_password_page_id' => 'Change Password',
        'woocommerce_logout_page_id' => 'Logout',
        'woocommerce_lost_password_page_id' => 'Lost Password'
    );
    foreach ($woopages as $woo_page_name => $woo_page_title) {
        $woopage = get_page_by_title($woo_page_title);
        if (isset($woopage->ID) && $woopage->ID) {
            update_option($woo_page_name, $woopage->ID); // Front Page
        }
    }
    // We no longer need to install pages
    delete_option('_wc_needs_pages');
    delete_transient('_wc_activation_redirect');

    // Flush rules after install
    flush_rewrite_rules();

}
function inwave_import_ap_backround()
{
    global $wpdb, $wp_filesystem;

    // Import advanced background
    $apb_data = get_template_directory() . '/admin/importer/data/apbackground.json';
    $apb_data = $wp_filesystem->get_contents( $apb_data );
    $export_array = json_decode($apb_data, true);
    foreach ( $export_array as $item ) {
        $wpdb->insert($wpdb->prefix.'ap_background', $item);
    }
}
function inwave_import_event()
{
    global $wp_filesystem;
    // Import eventon settings
    $data = get_template_directory() . '/admin/importer/data/eventon_settings.txt';
    $data = $wp_filesystem->get_contents( $data );
    $data = unserialize($data);
    update_option('evcal_options_evcal_1',$data);
}
function inwave_import_sliders(){

    global $wpdb,$wp_filesystem;

    // Import master sliders
    require_once( WP_PLUGIN_DIR . '/masterslider/admin/includes/classes/class-msp-importer.php' );

    $ms_importer = new MSP_Importer();
    $sliderdata = get_template_directory() . '/admin/importer/data/masterslider.json';

    $sliderdata = $wp_filesystem->get_contents( $sliderdata );

    $export_array =  $ms_importer->decode_import_data( $sliderdata);

    $ms_importer->last_new_slider_id = null;

    if( isset( $export_array['preset_styles'] ) && ! empty( $export_array['preset_styles'] ) ) {
        msp_update_option( 'preset_style'  , $export_array['preset_styles'] );
    }
    if( isset( $export_array['preset_effects'] ) && ! empty( $export_array['preset_effects'] ) ) {
        msp_update_option( 'preset_effect'  , $export_array['preset_effects'] );
    }
    global $mspdb;
    $table_name = $wpdb->prefix . "postmeta";
    foreach ( $export_array['sliders_data'] as $slider_id => $slider_fields ) {
        $slider_fields['status'] = current_user_can( 'publish_masterslider' ) ? 'published' : 'draft';
        $new_slider_id = $mspdb->add_slider( $slider_fields );
        $wpdb->update($table_name, array('meta_value'=> intval($new_slider_id)), array('meta_key' =>'inwave_masterslider', 'meta_value'=>intval($slider_id)));

        msp_update_slider_custom_css_and_fonts( $new_slider_id );
    }

    $uploads   = wp_upload_dir();
    $css_dir   = apply_filters( 'masterslider_custom_css_dir', $uploads['basedir'] . '/' . MSWP_SLUG );
    $css_file  = $css_dir . '/custom.css';
    @unlink($css_file);
    msp_save_custom_styles();
}
function inwave_import_course(){
    global $wpdb, $wp_filesystem;
    // Import courses
    $iw_data = get_template_directory() . '/admin/importer/data/iwcourse.json';
    $iw_data = $wp_filesystem->get_contents($iw_data);
    $export_array = json_decode($iw_data, true);
    foreach ( $export_array as $table => $list ) {
        foreach($list as $item) {
            switch($table){

                case 'iw_courses_extrafields_category':
                    if($item['category_alias']){
                        $category = get_term_by('slug', $item['category_alias'], 'iw_courses_class');
                        $catid = $category->term_id;
                    }else{
                        $catid = 0;
                    }
                    unset($item['category_alias']);
                    $item['category_id'] = $catid;
                    $wpdb->insert($wpdb->prefix . $table, $item);
                    break;
                case 'iw_courses_extrafields_value':
                    $courseid = '';
                    if($item['couser_slug']){
                        $course = $posts = get_posts(array(
                            'name' => $item['couser_slug'],
                            'posts_per_page' => 1,
                            'post_type' => 'iw_courses'
                        ));
                        $courseid = $course[0]->ID;

                    }
                    if($courseid){
                        unset($item['couser_slug']);
                        $item['courses_id'] = $courseid;
                        $wpdb->insert($wpdb->prefix . $table, $item);
                    }

                    break;
                default:
                    $wpdb->insert($wpdb->prefix . $table, $item);
                    break;
            }
        }

    }
}
function inwave_import_widgets(){
    global $wp_filesystem;
    // Add sidebar widget areas
    $sidebars = array(
        'sidebar-default' => 'Sidebar Default',
        'sidebar-woocommerce' => 'Sidebar Product Page',
        'sidebar-cart' => 'Slidebar Cart',
        'sidebar-eventon' => 'Sidebar Eventon',
        'sidebar-footer1' => 'Sidebar Footer Default',
        'sidebar-footer2' => 'Sidebar Footer Store'
    );
    update_option( 'sbg_sidebars', $sidebars );

    iwMainClass::widgets_init();

    // Add data to widgets
    $widgets_json = get_template_directory() . '/admin/importer/data/widget_data.json'; // widgets data file
    $widget_data = $wp_filesystem->get_contents( $widgets_json );
    inwave_import_widget_data($widget_data);
}

function inwave_import_response($code, $message,$percent = 0)
{
    $response = array();
    $response['code'] = $code;
    $response['msg'] = $message;
    $response['percent'] = $percent.'%';
    echo json_encode($response);
    exit;
}

// Parsing Widgets Function
// Thanks to http://wordpress.org/plugins/widget-settings-importexport/
function inwave_import_widget_data($widget_data)
{
    $json_data = $widget_data;
    $json_data = json_decode($json_data, true);

    $sidebar_data = $json_data[0];
    $widget_data = $json_data[1];

    // binding menu id again for custom menu widget
    $menus = wp_get_nav_menus();
    $new_wg = array();
    foreach ($widget_data as $key => $tp_widgets) {
        if ($key == 'nav_menu') {
            foreach ($tp_widgets as $key => $tp_widget) {
                foreach ($menus as $menu) {
                    if ($tp_widget['title'] == $menu->name) {
                        $tp_widget['nav_menu'] = $menu->term_id;
                        break;
                    }
                }
                $new_wg[$key] = $tp_widget;
            }
            $widget_data['nav_menu'] = $new_wg;
        }
    }

    foreach ($widget_data as $widget_data_title => $widget_data_value) {
        $widgets[$widget_data_title] = '';
        foreach ($widget_data_value as $widget_data_key => $widget_data_array) {
            if (is_int($widget_data_key)) {
                $widgets[$widget_data_title][$widget_data_key] = 'on';
            }
        }
    }
    unset($widgets[""]);

    foreach ($sidebar_data as $title => $sidebar) {
        $count = count($sidebar);
        for ($i = 0; $i < $count; $i++) {
            $widget = array();
            $widget['type'] = trim(substr($sidebar[$i], 0, strrpos($sidebar[$i], '-')));
            $widget['type-index'] = trim(substr($sidebar[$i], strrpos($sidebar[$i], '-') + 1));
            if (!isset($widgets[$widget['type']][$widget['type-index']])) {
                unset($sidebar_data[$title][$i]);
            }
        }
        $sidebar_data[$title] = array_values($sidebar_data[$title]);
    }

    foreach ($widgets as $widget_title => $widget_value) {
        foreach ($widget_value as $widget_key => $widget_value) {
            $widgets[$widget_title][$widget_key] = $widget_data[$widget_title][$widget_key];
        }
    }

    $sidebar_data = array(array_filter($sidebar_data), $widgets);

    inwave_parse_import_data($sidebar_data);
}

function inwave_parse_import_data($import_array)
{
    global $wp_registered_sidebars;
    $sidebars_data = $import_array[0];
    $widget_data = $import_array[1];
    $current_sidebars = get_option('sidebars_widgets');
    $new_widgets = array();

    foreach ($sidebars_data as $import_sidebar => $import_widgets) :

        foreach ($import_widgets as $import_widget) :
            //if the sidebar exists
            if (isset($wp_registered_sidebars[$import_sidebar])) :
                $title = trim(substr($import_widget, 0, strrpos($import_widget, '-')));
                $index = trim(substr($import_widget, strrpos($import_widget, '-') + 1));
                $current_widget_data = get_option('widget_' . $title);
                $new_widget_name = inwave_get_new_widget_name($title, $index);
                $new_index = trim(substr($new_widget_name, strrpos($new_widget_name, '-') + 1));

                if (!empty($new_widgets[$title]) && is_array($new_widgets[$title])) {
                    while (array_key_exists($new_index, $new_widgets[$title])) {
                        $new_index++;
                    }
                }
                $current_sidebars[$import_sidebar][] = $title . '-' . $new_index;
                if (array_key_exists($title, $new_widgets)) {
                    $new_widgets[$title][$new_index] = $widget_data[$title][$index];
                    $multiwidget = $new_widgets[$title]['_multiwidget'];
                    unset($new_widgets[$title]['_multiwidget']);
                    $new_widgets[$title]['_multiwidget'] = $multiwidget;
                } else {
                    $current_widget_data[$new_index] = $widget_data[$title][$index];

                    $current_multiwidget = isset($current_widget_data['_multiwidget']) ? $current_widget_data['_multiwidget'] : '';
                    $new_multiwidget = isset($widget_data[$title]['_multiwidget']) ? $widget_data[$title]['_multiwidget'] : false;
                    $multiwidget = ($current_multiwidget != $new_multiwidget) ? $current_multiwidget : 1;
                    unset($current_widget_data['_multiwidget']);
                    $current_widget_data['_multiwidget'] = $multiwidget;
                    $new_widgets[$title] = $current_widget_data;
                }

            endif;
        endforeach;
    endforeach;

    if (isset($new_widgets) && isset($current_sidebars)) {
        update_option('sidebars_widgets', $current_sidebars);

        foreach ($new_widgets as $title => $content)
            update_option('widget_' . $title, $content);

        return true;
    }

    return false;
}

function inwave_get_new_widget_name($widget_name, $widget_index)
{
    $current_sidebars = get_option('sidebars_widgets');
    $all_widget_array = array();
    foreach ($current_sidebars as $sidebar => $widgets) {
        if (!empty($widgets) && is_array($widgets) && $sidebar != 'wp_inactive_widgets') {
            foreach ($widgets as $widget) {
                $all_widget_array[] = $widget;
            }
        }
    }
    while (in_array($widget_name . '-' . $widget_index, $all_widget_array)) {
        $widget_index++;
    }
    $new_widget_name = $widget_name . '-' . $widget_index;
    return $new_widget_name;
}


// Rename sidebar
function inwave_name_to_class($name)
{
    $class = str_replace(array(' ', ',', '.', '"', "'", '/', "\\", '+', '=', ')', '(', '*', '&', '^', '%', '$', '#', '@', '!', '~', '`', '<', '>', '?', '[', ']', '{', '}', '|', ':',), '', $name);
    return $class;
}