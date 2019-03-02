<?php
if (have_posts()) : while (have_posts()) : the_post();
    iw_course_get_template_part('course');
endwhile;
endif;

