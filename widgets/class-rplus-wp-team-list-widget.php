<?php
/**
 * Recent Posts per Category Widget
 *
 * @since 2.8.0
 */
class WP_Team_List_Widget extends WP_Widget {

        function __construct() {
                $widget_ops = array('classname' => 'widget_wp_team_list', 'description' => __( "Display users as team members.", "rplus-wp-team-list" ) );
                parent::__construct('wp-team-list', __( 'WP Team List', 'rplus-wp-team-list' ), $widget_ops);
                $this->alt_option_name = 'widget_wp_team_list';

                add_action( 'save_post', array($this, 'flush_widget_cache') );
                add_action( 'deleted_post', array($this, 'flush_widget_cache') );
                add_action( 'switch_theme', array($this, 'flush_widget_cache') );
                add_action( 'show_user_profile', array( $this, 'flush_widget_cache' ) );
                add_action( 'edit_user_profile', array( $this, 'flush_widget_cache' ) );
        }

        function widget($args, $instance) {
                $cache = wp_cache_get( 'widget_wp_team_list', 'widget' );

                if ( !is_array($cache) )
                        $cache = array();

                if ( ! isset( $args['widget_id'] ) )
                        $args['widget_id'] = $this->id;

                if ( isset( $cache[ $args['widget_id'] ] ) ) {
                        echo $cache[ $args['widget_id'] ];
                        return;
                }

                ob_start();
                extract($args);

                $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Editors', 'rplus-wp-team-list' );
                $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

                $role = ( ! empty( $instance['role'] ) ) ? $instance['role'] : 'editor';

                $show_link = isset( $instance['show_link'] ) ? $instance['show_link'] : false;
                $page_link = isset( $instance['page_link'] ) ? absint( $instance['page_link'] ) : 7;

                $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 3;

                if ( ! $number )
                    $number = 3;
?>
                <?php echo $before_widget; ?>
                <?php if ( $title ) echo $before_title . $title . $after_title; ?>
                <div class="mini-team-list">
                    <?php rplus_wp_team_list( array( 'role' => $role, 'number' => $number ), true, 'rplus-wp-team-list-widget.php' ); ?>
                    <?php if ( $show_link ) : ?>
                        <a href="<?php echo esc_url( get_permalink( $page_link ) ); ?>" class="show-all"><?php _e( 'Show all Team Members', 'rplus-wp-team-list' ); ?></a>
                    <?php endif; ?>
                </div>
                <?php echo $after_widget; ?>
<?php

                $cache[$args['widget_id']] = ob_get_flush();
                wp_cache_set('widget_wp_team_list', $cache, 'widget');
        }

        function update( $new_instance, $old_instance ) {

                $instance = $old_instance;
                $instance['title']      = strip_tags($new_instance['title']);
                $instance['role']       = strip_tags($new_instance['role']);
                $instance['number']     = (int) $new_instance['number'];
                $instance['show_link']  = isset( $new_instance['show_link'] ) ? (bool) $new_instance['show_link'] : false;
                $instance['page_link']  = (int) $new_instance['page_link'];


                $this->flush_widget_cache();

                $alloptions = wp_cache_get( 'alloptions', 'options' );

                if ( isset($alloptions['widget_wp_team_list']) )
                        delete_option('widget_wp_team_list');

                return $instance;
        }

        function flush_widget_cache() {
                wp_cache_delete( 'widget_wp_team_list', 'widget' );
        }

        function form( $instance ) {
                $title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
                $role      = isset( $instance['role'] ) ? esc_attr( $instance['role'] ) : 'editor';
                $number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 3;
                $show_link = isset( $instance['show_link'] ) ? (bool) $instance['show_link'] : false;
                $page_link = isset( $instance['page_link'] ) ? (bool) $instance['page_link'] : 7;
?>
                <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'rplus-wp-team-list' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

                <p><label for="<?php echo $this->get_field_id( 'role' ); ?>"><?php _e( 'Role:', 'rplus-wp-team-list' ); ?></label>
                <select id="<?php echo $this->get_field_id( 'role' ); ?>" name="<?php echo $this->get_field_name( 'role' ); ?>" class="widefat">
                <?php wp_dropdown_roles( $role ); ?>
                </select></p>

                <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of members to show:', 'rplus-wp-team-list' ); ?></label>
                <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

                <p><input class="checkbox" type="checkbox" <?php checked( $show_link ); ?> id="<?php echo $this->get_field_id( 'show_link' ); ?>" name="<?php echo $this->get_field_name( 'show_link' ); ?>" />
                <label for="<?php echo $this->get_field_id( 'show_link' ); ?>"><?php _e( 'Show link to team page?', 'rplus-wp-team-list' ); ?></label></p>

                <p><label for="<?php echo $this->get_field_id( 'page_link' ); ?>"><?php _e( 'Link to:', 'rplus-wp-team-list' ); ?></label>
                <select id="<?php echo $this->get_field_id( 'page_link' ); ?>" name="<?php echo $this->get_field_name( 'page_link' ); ?>" class="widefat">
                <?php
                    $pages = get_pages( array( 'orderby' => 'name', 'parent' => 0 ) );
                    foreach ( $pages as $page_link ) {
                        $option = '<option value="' . $page_link->ID . '" '. selected( $instance['page_link'], $page_link->ID ) . '>';
                        $option .= $page_link->post_title;
                        $option .= '</option>';
                        echo $option;
                    }
                ?>
                </select></p>
<?php
        }
}