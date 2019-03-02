<?php
/**
 * Created by PhpStorm.
 * User: HungTx
 * Date: 4/7/2015
 * Time: 8:58 AM
 */
class AthleteHelper{
    /**
     * CUT STRING BY CHARACTERS FUNCTION
     * @param $text
     * @param int $length
     * @param string $replacer
     * @param bool $isStrips
     * @param string $stringtags
     * @return string
     */
    public static function substring($text, $length = 100, $isStrips = false, $replacer = '...', $stringtags = '') {

        if($isStrips){
            $text = preg_replace('/\<p.*\>/Us','',$text);
            $text = str_replace('</p>','<br/>',$text);
            $text = strip_tags($text, $stringtags);
        }

        if(function_exists('mb_strlen')){
            if (mb_strlen($text) < $length)	return $text;
            $text = mb_substr($text, 0, $length);
        }else{
            if (strlen($text) < $length)	return $text;
            $text = substr($text, 0, $length);
        }

        return $text . $replacer;
    }

    /**
     * CUT STRING BY WORDS FUNCTION
     * @param $text
     * @param int $length
     * @param string $replacer
     * @param bool $isStrips
     * @param string $stringtags
     * @return string
     */
    public static function substrword($text, $length = 100, $isStrips = false, $replacer = '...', $stringtags = '') {
        if($isStrips){
            $text = preg_replace('/\<p.*\>/Us','',$text);
            $text = str_replace('</p>','<br/>',$text);
            $text = strip_tags($text, $stringtags);
        }
        $tmp = explode(" ", $text);

        if (count($tmp) < $length)
            return $text;

        $text = implode(" ", array_slice($tmp, 0, $length)) . $replacer;

        return $text;
    }
}

/** Base on https://wordpress.org/plugins/shortcode-widget/ */
class Inwave_Widget_Shortcodes extends WP_Widget {

    function __construct() {
        $widget_ops = array('classname' => 'shortcode_widget', 'description' => __('Shortcode or Arbitrary Text', 'inwavethemes'));
        $control_ops = array('width' => 400, 'height' => 350);
        parent::__construct('shortcode-widget', __('Shortcode Widget', 'inwavethemes'), $widget_ops, $control_ops);
    }

    function widget( $args, $instance ) {
        extract($args);
        $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
        $text = do_shortcode(apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance ));
        echo wp_kses_post($before_widget);
        if ( !empty( $title ) ) { echo wp_kses_post($before_title . $title . $after_title); } ?>
        <div class="textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
        <?php
        echo wp_kses_post($after_widget);
    }

    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        if ( current_user_can('unfiltered_html') )
            $instance['text'] =  $new_instance['text'];
        else
            $instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
        $instance['filter'] = isset($new_instance['filter']);
        return $instance;
    }

    function form( $instance ) {
        $instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' ) );
        $title = strip_tags($instance['title']);
        $text = esc_textarea($instance['text']);
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'inwavethemes'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>

        <textarea class="widefat" rows="16" cols="20" id="<?php echo esc_attr($this->get_field_id('text')); ?>" name="<?php echo esc_attr($this->get_field_name('text')); ?>"><?php echo wp_kses_post($text); ?></textarea>

        <p><input id="<?php echo esc_attr($this->get_field_id('filter')); ?>" name="<?php echo esc_attr($this->get_field_name('filter')); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo esc_attr($this->get_field_id('filter')); ?>"><?php _e('Automatically add paragraphs', 'inwavethemes'); ?></label></p>
    <?php
    }
}
function inwave_widgets_registration() {
    register_widget('Inwave_Widget_Shortcodes');
}
add_action('widgets_init', 'inwave_widgets_registration');