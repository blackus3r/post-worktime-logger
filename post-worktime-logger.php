<?php
/**
 *
 * @copyright Patrick Hausmann
 * @author Patrick Hausmann <privat@patrck-designs.de>
 */

/*
Plugin Name: Post Worktime Logger
Plugin URI: https://wordpress.org/plugins/post-worktime-logger/
Description: A plugin to track the worktime for each post.
Version: 1.2.2
Author: Patrick Hausmann
Author URI: https://profiles.wordpress.org/filme-blog/
License: GPLv2 or later
Text Domain: post-worktime-logger
*/

if ( !defined('PLUGINDIR') )
	define( 'PLUGINDIR', 'wp-content/plugins' );

include_once (__DIR__."/widget.php");
include_once (__DIR__."/settings.php");

$pwlOptions = get_option("post-worktime-logger-options");

/**
 * Handles the ping from frontend to track the worktime.
 */
function pwlHandleWorktimePing()
{
    if (is_user_logged_in())
    {
        $postId = $_POST['currentPostId'];

        if (is_numeric($postId))
        {
            $post = get_post($postId);

            if ($post)
            {
                $oldWorktime = get_post_meta($postId, "post-worktime", true);
                if ($oldWorktime===false) $oldWorktime = 0;

                $lastPingTimeStamp = get_option("post-worktime-logger-last-ping-timestamp");
                if (!$lastPingTimeStamp) $lastPingTimeStamp = time()-60;

                $workingTimeSinceLastPing = time()-$lastPingTimeStamp;
                if ($workingTimeSinceLastPing>60) $workingTimeSinceLastPing = 60;

                $newWorktime = $oldWorktime+$workingTimeSinceLastPing;

                update_post_meta($postId, "post-worktime", $newWorktime);

            }
        }

        update_option("post-worktime-logger-last-ping-timestamp", time());
    }
}

/**
 * Converts seconds to a human readable string.
 *
 * @param int $seconds the seconds to convert.
 *
 * @return string the human readable string.
 */
function pwlSecondsToHumanReadableTime($seconds)
{
	$dtF = new \DateTime('@0');
	$dtT = new \DateTime("@$seconds");
	return $dtF->diff($dtT)->format('%H:%I:%S');
}

/**
 * Renders the post meta box.
 */
function pwlRenderMetaBoxSummary()
{
	$content = "";

    if (isset($_GET['post']))
    {
        $postId = $_GET['post'];

        if (is_numeric($postId))
        {
            $post = get_post($postId);
            if ($post)
            {
                $currentPostId = $_GET['post'];

                $worktime = get_post_meta($currentPostId, "post-worktime", true);
                if (!$worktime)
                {
                    update_post_meta( $currentPostId, "post-worktime", 0 );
                    $worktime = $worktime = get_post_meta($currentPostId, "post-worktime", true);
                }

                $content.=pwlGetPostWorktimeLoggerControlBox($worktime, $currentPostId);
            }
        }
    }
    else $content = __("You have to save the post, before we can track the time.", "post-worktime-logger");


    echo $content;
}

/**
 * Creates the control box for post worktime logger.
 *
 * @param int $_totalWorktime the total worktime.
 * @param int $_postId the post id.
 * @return string the html code for the control box.
 */
function pwlGetPostWorktimeLoggerControlBox($_totalWorktime, $_postId)
{
    $content = "";
    $content.= "<span style=\"display:none;\" id=\"post-worktime-logger-current-post-id\">".$_postId."</span>";
    $content .= __('Current worktime', "post-worktime-logger").': <span id="frontendTime">0</span><br />';
    $content .= __("Total worktime", "post-worktime-logger").': <span id="serverWorktime">';
    $content .= pwlSecondsToHumanReadableTime($_totalWorktime);
    $content .= '</span><br />';

    if (isControlBoxEnabled())
    {
        $content .= '<button class="button button-small pwl-button" id="pwl-pause-button">'.__("Pause", "post-worktime-logger").'</button>';
        $content .= '<button class="button button-small pwl-button" style="display:none;" id="pwl-resume-button">'.__("Resume", "post-worktime-logger").'</button>';
        $content .= '<button class="button button-small pwl-button" id="pwl-reset-button">'.__("Reset", "post-worktime-logger").'</button>';
    }

    return $content;
}

/**
 * Adds the meta box to post editor page.
 */
function pwlAddMetaBoxSummary()
{
    add_meta_box(
        'post-worktime-logger-meta-box',
        __( 'Post Worktime', "post-worktime-logger"),
        'pwlRenderMetaBoxSummary'
    );
}

/**
 * Creates the column header.
 *
 * @param $_columns
 * @return array
 */
function pwPostsPageHeader($_columns)
{
    return array_merge( $_columns,
        array('pwlworktimecolumn' => __('Worktime', "post-worktime-logger")) );
}

/**
 * Renders the worktime of the current post.
 * @param $_column
 * @param $post_id
 */
function pwlWorktimeColumnRenderer($_column, $post_id)
{
    if ($_column == "pwlworktimecolumn")
    {
        $worktime = get_post_meta($post_id, "post-worktime", true);

        if ($worktime)
        {
            echo pwlSecondsToHumanReadableTime($worktime);
        }
        else echo "00:00:00";
    }
}

/**
 * Makes our column sortable.
 *
 * @param $_sortableColumns
 * @return array
 */
function pwlSortableColumn( $_sortableColumns )
{
    $_sortableColumns[ 'pwlworktimecolumn' ] = 'post-worktime';

    return $_sortableColumns;
}

/**
 * Sorting function.
 *
 * @param $_vars
 * @return array
 */
function pwlWorktimeOrderBy( $_vars )
{
    if (isset($_vars['orderby']) && 'post-worktime' == $_vars['orderby']) {
        $_vars = array_merge($_vars, array(
            'meta_key' => 'post-worktime',
            'orderby' => 'meta_value_num'
        ));
    }

    return $_vars;
}

/**
 * Clear the work time.
 */
function pwlHandleWorktimeReset()
{
    if (is_user_logged_in())
    {
        $postId = $_POST['currentPostId'];

        if (is_numeric($postId))
        {
            $post = get_post($postId);

            if ($post)
            {
                update_post_meta($postId, "post-worktime", 0);
            }
        }
        update_option("post-worktime-logger-last-ping-timestamp", time());
    }
}

/**
 * Checks if the control box is enabled and returns true, otherwise false.
 *
 * @return bool
 */
function isControlBoxEnabled()
{
    global $pwlOptions;

    if (isset($pwlOptions["enableControlButtons"]) && $pwlOptions["enableControlButtons"]=="on")
    {
        return true;
    }
    else return false;
}

//Register post meta box
add_action( 'add_meta_boxes', 'pwlAddMetaBoxSummary');

//Register Ajax Ping from frontend
add_action( 'wp_ajax_worktime_ping', 'pwlHandleWorktimePing');
add_action( 'wp_ajax_worktime_reset', 'pwlHandleWorktimeReset');


add_action( 'init', function () {
    $domain = 'post-worktime-logger';
    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
    if ( $loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' ) )
    {
        return $loaded;
    }
    else
    {
        load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
    }
} );

//Register admin javascript file
add_action("admin_enqueue_scripts", function ($hook)
{
    wp_enqueue_style("post-worktime-logger", plugins_url( "resources/css/post-worktime-logger.css", __FILE__ ));

	if ($hook=="post.php")
	{
		wp_enqueue_script("post-worktime-logger", plugins_url( "resources/js/post-worktime-logger.js", __FILE__ ));
        $pwt_options = get_option("post-worktime-logger-options");

        wp_localize_script( 'post-worktime-logger', 'pwl', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'inactivityTimeout' => ( ! empty( $pwt_options['inactivityTimeout'] ) ) ? esc_html( $pwt_options['inactivityTimeout'] ) : '5',
        ) );
	}
});

//Register scripts for frontend
add_action("wp_enqueue_scripts", function () {
    wp_enqueue_style("post-worktime-logger", plugins_url( "resources/css/post-worktime-logger.css", __FILE__ ));

    if (is_user_logged_in())
    {//only include js if the user is logged in.
        wp_enqueue_script("post-worktime-logger", plugins_url( "resources/js/post-worktime-logger.js", __FILE__ ), array("jquery"));
        wp_localize_script( 'post-worktime-logger', 'pwl',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
        );
    }
});

add_filter( 'request', 'pwlWorktimeOrderBy' );
add_filter( 'manage_edit-post_sortable_columns', 'pwlSortableColumn' );
// For registering the column
add_filter( 'manage_posts_columns', 'pwPostsPageHeader' );
// For rendering the column
add_action( 'manage_posts_custom_column', 'pwlWorktimeColumnRenderer', 10, 2 );

//Register frontend widget
add_action('widgets_init', function(){
    register_widget('PwlFrontendWidget');
});

if(is_admin())
{
    new PostWorktimeLoggerSettingsPage();
}
