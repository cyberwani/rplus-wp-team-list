<?php
/**
 * WP Team List
 *
 * This template file is used to generate the output for the WP Team List template
 * function and shortcode, it can be overwritten in your theme or child theme, if
 * you need different markup.
 *
 * To change the WP Team List Markup rendered on the frontend, copy this file in your
 * theme folder. The final folder structure would be something like this:
 *
 * /wp-content/themes/<your theme>/rplus-wp-team-list.php
 *
 * The plugin will look in your theme or child theme first and will fallback to
 * the file in the plugins folder.
 *
 * @package   WP Team List
 * @author    silvan <silvan@required.ch>
 * @license   GPL-2.0+
 * @link      https://github.com/wearerequired/wp-team-list/
 * @copyright 2013 required gmbh
 */

/**
 * Let's begin shall we?
 *
 * You have access to the WP_User object through $user. It's for you to play
 * and render information about your team member that you want to.
 *
 * @param   int     $user      WP_User object for the current team member.
 *
 *          Info:   This plugin is targeted to advanced WordPress themers with a
 *                  good unterstanding of PHP and WordPress. Nevertheless here are a few
 *                  examples of what you could use in this template:
 *
 *          $user:  See what is in the user object <?php var_dump( $user ); ?>
 *                  $user->data is were most of the useful info lives.
 *                  $user->roles[0] gives back the user role on this blog.
 *                  $user->ID should be clear.
 *
 *          Meta:   get_user_meta( $user->ID, $key = '', $single = false )
 *                  All the other meta information about your user is available
 *                  through get_user_meta() like the author bio:
 *                  echo get_user_meta( $user->ID, 'description', true );
 *
 */
?>
<!-- START: templates/rplus-wp-team-list -->
<div class="<?php rplus_wp_team_list_classes( array( 'author-' . $user->ID, 'role-' . $user->roles[0] ) ); ?>">

    <figure class="author-image">
        <?php echo get_avatar( $user->ID, $size = '92', $default = '', $alt = $user->data->display_name ); ?>
    </figure>

    <h2><?php echo esc_html( $user->data->display_name ); ?></h2>
    <p><?php echo esc_html( ucfirst( $user->roles[0] ) ); ?></p>

    <?php
        /**
         * Author description
         *
         * Renders the author bio if the user added them in their profile.
         * Uses the user_meta table to get the info
         *
         * @uses get_user_meta( $user_id, $key = '', $single = false )
         *
         * @todo Add filter for markup rendering.
         */
        if ( '' != get_user_meta( $user->ID, 'description', true ) ) : ?>
            <p class="team-member-bio"><?php echo get_user_meta( $user->ID, 'description', true ); ?></p><?php
        endif; ?>

    <p class="team-member-meta">
        <a href="<?php echo esc_url( get_author_posts_url( $user->ID ) ); ?>"
           title="<?php printf( esc_attr__( 'View all posts by %s', 'rplus-wp-team-list' ), $user->display_name ); ?>">
           <?php printf( esc_html__( 'View all %s articles', 'rplus-wp-team-list' ), count_user_posts( $user->ID ) ); ?>
        </a>
    </p>
</div>
<!-- END: templates/rplus-wp-team-list -->