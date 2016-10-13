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
Version: 1.0
Author: Patrick Hausmann
Author URI: https://profiles.wordpress.org/filme-blog/
License: GPLv2 or later
Text Domain: post-worktime-logger
*/

if ( !defined('PLUGINDIR') )
	define( 'PLUGINDIR', 'wp-content/plugins' );

/**
 * Handles the ping from frontend to track the worktime.
 */
function pwlHandleWorktimePing()
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
    $postId = $_GET['post'];

    if (isset($_GET['post']))
    {
        if (is_numeric($postId))
        {
            $post = get_post($postId);
            if ($post)
            {
                $currentPostId = $_GET['post'];

                $content.= "<span style=\"display:none;\" id=\"post-worktime-logger-current-post-id\">".$currentPostId."</span>";

                $worktime = get_post_meta($currentPostId, "post-worktime", true);

                if (!$worktime)
                {
                    update_post_meta( $currentPostId, "post-worktime", 0 );
                    $worktime = $worktime = get_post_meta($currentPostId, "post-worktime", true);

                }

                $content .= __('Current worktime', "post-worktime-logger").': <span id="frontendTime">0</span><br />';

                $content .= '<span id="serverWorktime">';
                $content .= __("Total worktime", "post-worktime-logger").": ".pwlSecondsToHumanReadableTime($worktime);
                $content .= '</span><br />';
            }
        }
    }
    else $content = __("You have to save the post, before we can track the time.", "post-worktime-logger");


    echo $content;
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
        array('pwlworktimecolumn' => __('Worktime')) );
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
    if ( isset( $_vars['orderby'] ) && 'post-worktime' == $_vars['orderby'] )
    {
        $_vars = array_merge( $_vars, array(
            'meta_key' => 'post-worktime',
            'orderby' => 'meta_value_num'
        ) );
    }

    return $_vars;
}

//Register post meta box
add_action( 'add_meta_boxes', 'pwlAddMetaBoxSummary');

//Register Ajax Ping from frontend
add_action( 'wp_ajax_worktime_ping', 'pwlHandleWorktimePing');

//Load language
load_plugin_textdomain( 'post-worktime-logger', false, plugins_url('/lang/', __FILE__));

//Register admin javascript file
add_action("admin_enqueue_scripts", function ($hook) {
	if ($hook=="post.php")
	{
		wp_enqueue_script("post-worktime-logger", plugins_url( "resources/js/post-worktime-logger.js", __FILE__ ));
	}
});

add_filter( 'request', 'pwlWorktimeOrderBy' );
add_filter( 'manage_edit-post_sortable_columns', 'pwlSortableColumn' );
// For registering the column
add_filter( 'manage_posts_columns', 'pwPostsPageHeader' );
// For rendering the column
add_action( 'manage_posts_custom_column', 'pwlWorktimeColumnRenderer', 10, 2 );
