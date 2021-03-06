<?php
add_action( 'widgets_init', 'lds_focus_mode_widget_areas' );
function lds_focus_mode_widget_areas() {
    register_sidebar( array(
        'name' => __( 'LearnDash Focus Mode: Right Sidebar', 'lds-skins' ),
        'id' => 'lds-focus-mode-right-sidebar',
        'description' => __( 'Widgets in this area will be shown to the right of the content in focus mode', 'lds-skins' ),
        'before_widget' => '<div class="lds-focus-content-widget">',
        'after_widget' => '</div>',
        'before_title' => '<div class="lds-focus-widget-title" role="heading" aria-level="3">',
        'after_title'  => '</div>'
    ) );
    register_sidebar( array(
        'name' => __( 'LearnDash Focus Mode: Below Menu', 'lds-skins' ),
        'id' => 'lds-focus-mode-menu-sidebar',
        'description' => __( 'Widgets in this area will be shown below the course menu on the left side', 'lds-skins' ),
        'before_widget' => '<div class="lds-focus-sidebar-widget">',
        'after_widget' => '</div>',
        'before_title' => '<span class="ld-focus-widget-heading" role="heading" aria-level="2">',
        'after_title'  => '</span>'
    ) );
}

add_action( 'learndash-focus-sidebar-after-nav-wrapper', 'lds_focus_mode_below_menu_widget_area' );
function lds_focus_mode_below_menu_widget_area() {

    if ( is_active_sidebar('lds-focus-mode-menu-sidebar') ) : ?>
        <div class="lds-focus-sidebar-widgets">
            <?php
            dynamic_sidebar('lds-focus-mode-menu-sidebar'); ?>
        </div>
    <?php endif;
}

add_action( 'learndash-focus-masthead-after', 'lds_focus_mode_sidebar_widget_area' );
function lds_focus_mode_sidebar_widget_area() {

    if ( is_active_sidebar('lds-focus-mode-right-sidebar') ) : ?>
        <div class="lds-focus-content-widgets">
            <?php
            dynamic_sidebar('lds-focus-mode-right-sidebar'); ?>
        </div>
    <?php endif;

}

add_filter( 'learndash_wrapper_class', 'lds_add_focus_widget_classes' );
function lds_add_focus_widget_classes( $classes ) {

    if( !is_active_sidebar('lds-focus-mode-right-sidebar') ) {
        return $classes;
    }

    return $classes .= ' lds-focus-mode-content-widgets';

}

if( class_exists( 'WP_Widget' ) ) {

     class lds_LearnDash_Outline_Course_Navigation_Widget extends WP_Widget {

          /**
		 * Setup Course Navigation Widget
		 */
		public function __construct() {
			$widget_ops  = array(
				'classname'   => 'widget_ldcoursenavigation',
				// translators: placeholder: Course.
				'description' => sprintf( esc_html_x( 'LearnDash Outline - %s Navigation. Shows lessons and topics on the current course in the outline style.', 'placeholder: Course', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ),
			);
			$control_ops = array();
			// translators: placeholder: Course.
			parent::__construct( 'widget_ldcoursenavigation', sprintf( esc_html_x( 'Outline %s Navigation', 'Course Navigation Label', 'lds_skins' ), LearnDash_Custom_Label::get_label( 'course' ) ), $widget_ops, $control_ops );
		}

          /**
           * Displays widget
           *
           * @since 2.1.0
           *
           * @param  array $args     widget arguments
           * @param  array $instance widget instance
           * @return string          widget output
           */
          public function widget( $args, $instance ) {

               global $learndash_shortcode_used;

               // global $post;
               $post = get_post( get_the_id() );

               if ( ( ! is_a( $post, 'WP_Post' ) ) || ( empty( $post->ID ) ) || ( ! is_single() ) ) {
                    return;
               }

               $course_id = learndash_get_course_id( $post->ID );
               if ( empty( $course_id ) ) {
                    return;
               }

               $instance['show_widget_wrapper'] = true;
               $instance['current_lesson_id']   = 0;
               $instance['current_step_id']     = 0;

               $lesson_query_args       = array();
               $course_lessons_per_page = learndash_get_course_lessons_per_page( $course_id );
               if ( $course_lessons_per_page > 0 ) {
                    if ( in_array( $post->post_type, array( 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' ), true ) ) {

                         $instance['current_step_id'] = $post->ID;
                         if ( 'sfwd-lessons' === $post->post_type ) {
                              $instance['current_lesson_id'] = $post->ID;
                         } elseif ( in_array( $post->post_type, array( 'sfwd-topic', 'sfwd-quiz' ), true ) ) {
                              $instance['current_lesson_id'] = learndash_course_get_single_parent_step( $course_id, $post->ID, 'sfwd-lessons' );
                         }

                         if ( ! empty( $instance['current_lesson_id'] ) ) {
                              $course_lesson_ids = learndash_course_get_steps_by_type( $course_id, 'sfwd-lessons' );
                              if ( ! empty( $course_lesson_ids ) ) {
                                   $course_lessons_paged = array_chunk( $course_lesson_ids, $course_lessons_per_page, true );
                                   $lessons_paged        = 0;
                                   foreach ( $course_lessons_paged as $paged => $paged_set ) {
                                        if ( in_array( $instance['current_lesson_id'], $paged_set ) ) {
                                             $lessons_paged = $paged + 1;
                                             break;
                                        }
                                   }

                                   if ( ! empty( $lessons_paged ) ) {
                                        $lesson_query_args['pagination'] = 'true';
                                        $lesson_query_args['paged']      = $lessons_paged;
                                   }
                              }
                         } elseif ( in_array( $post->post_type, array( 'sfwd-quiz' ), true ) ) {
                              // If here we have a global Quiz. So we set the pager to the max number
                              $course_lesson_ids = learndash_course_get_steps_by_type( $course_id, 'sfwd-lessons' );
                              if ( ! empty( $course_lesson_ids ) ) {
                                   $course_lessons_paged       = array_chunk( $course_lesson_ids, $course_lessons_per_page, true );
                                   $lesson_query_args['paged'] = count( $course_lessons_paged );
                              }
                         }
                    }
               } else {
                    if ( in_array( $post->post_type, array( 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz' ), true ) ) {

                         $instance['current_step_id'] = $post->ID;
                         if ( 'sfwd-lessons' === $post->post_type ) {
                              $instance['current_lesson_id'] = $post->ID;
                         } elseif ( in_array( $post->post_type, array( 'sfwd-topic', 'sfwd-quiz' ), true ) ) {
                              $instance['current_lesson_id'] = learndash_course_get_single_parent_step( $course_id, $post->ID, 'sfwd-lessons' );
                         }
                    }
               }

               extract( $args );

               /** This filter is documented in https://developer.wordpress.org/reference/hooks/widget_title/ */
               $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );

               wp_register_style( 'lds-expanded', LDS_URL . '/assets/css/templates/expanded.css', array(), LDS_VER );
               wp_enqueue_style('lds-expanded');

               echo '<div class="learndash-wrapper lds-template-expanded">';

               echo $before_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML.

               if ( ! empty( $title ) ) {
                    echo $before_title . $title . $after_title; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML.
               }

               learndash_course_navigation( $course_id, $instance, $lesson_query_args );

               echo $after_widget; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML.

               echo '</div>';

               $learndash_shortcode_used = true;

          }

          /**
           * Handles widget updates in admin
           *
           * @since 2.1.0
           *
           * @param  array $new_instance
           * @param  array $old_instance
           * @return array $instance
           */
          public function update( $new_instance, $old_instance ) {
               $instance = $old_instance;

               $instance['title'] = wp_strip_all_tags( $new_instance['title'] );

               $instance['show_lesson_quizzes'] = isset( $new_instance['show_lesson_quizzes'] ) ? (bool) $new_instance['show_lesson_quizzes'] : false;
               $instance['show_topic_quizzes']  = isset( $new_instance['show_topic_quizzes'] ) ? (bool) $new_instance['show_topic_quizzes'] : false;
               $instance['show_course_quizzes'] = isset( $new_instance['show_course_quizzes'] ) ? (bool) $new_instance['show_course_quizzes'] : false;

               return $instance;
          }

          /**
           * Display widget form in admin
           *
           * @since 2.1.0
           *
           * @param  array $instance widget instance
           */
          public function form( $instance ) {
               $instance            = wp_parse_args( (array) $instance, array( 'title' => '' ) );
               $title               = wp_strip_all_tags( $instance['title'] );
               $show_lesson_quizzes = isset( $instance['show_lesson_quizzes'] ) ? (bool) $instance['show_lesson_quizzes'] : false;
               $show_topic_quizzes  = isset( $instance['show_topic_quizzes'] ) ? (bool) $instance['show_topic_quizzes'] : false;
               $show_course_quizzes = isset( $instance['show_course_quizzes'] ) ? (bool) $instance['show_course_quizzes'] : false;

               ?>
                    <p>
                         <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'learndash' ); ?></label>
                         <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
                    </p>
                    <p>
                         <input class="checkbox" type="checkbox" <?php checked( $show_course_quizzes ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_course_quizzes' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_course_quizzes' ) ); ?>" />
                         <label for="<?php echo esc_attr( $this->get_field_id( 'show_course_quizzes' ) ); ?>">
                         <?php
                         // translators: placeholders: Course, Quizzes.
                         echo sprintf( esc_html_x( 'Show %1$s %2$s?', 'placeholders: Course, Quizzes', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
                         ?>
                         </label>
                    </p>
                    <p>
                         <input class="checkbox" type="checkbox" <?php checked( $show_lesson_quizzes ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_lesson_quizzes' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_lesson_quizzes' ) ); ?>" />
                         <label for="<?php echo esc_attr( $this->get_field_id( 'show_lesson_quizzes' ) ); ?>">
                         <?php
                         // translators: placeholders: Lesson, Quizzes.
                         echo sprintf( esc_html_x( 'Show %1$s %2$s?', 'placeholders: Lesson, Quizzes', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
                         ?>
                         </label>
                    </p>
                    <p>
                         <input class="checkbox" type="checkbox" <?php checked( $show_topic_quizzes ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_topic_quizzes' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_topic_quizzes' ) ); ?>" />
                         <label for="<?php echo esc_attr( $this->get_field_id( 'show_topic_quizzes' ) ); ?>">
                         <?php
                         // translators: placeholders: Topic, Quizzes.
                         echo sprintf( esc_html_x( 'Show %1$s %2$s?', 'placeholders: Topic, Quizzes', 'learndash' ), LearnDash_Custom_Label::get_label( 'topic' ), LearnDash_Custom_Label::get_label( 'quizzes' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Method escapes output
                         ?>
                         </label>
                    </p>
               <?php
               }

          }

          add_action(
               'widgets_init',
               function() {
                    return register_widget( 'lds_LearnDash_Outline_Course_Navigation_Widget' );
               }
          );

}
