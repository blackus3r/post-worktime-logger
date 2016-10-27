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
Version: 1.2.3
Author: Patrick Hausmann
Author URI: https://profiles.wordpress.org/filme-blog/
License: GPLv3
Text Domain: post-worktime-logger
*/

if ( !defined('PLUGINDIR') )
	define( 'PLUGINDIR', 'wp-content/plugins' );

include_once (__DIR__."/widget.php");
include_once (__DIR__."/settings.php");

const PWL_NAME = "post-worktime-logger";
const PWL_TEXT_DOMAIN = "post-worktime-logger";

$pwlOptions = get_option("post-worktime-logger-options");

/**
 * Handles the ping from frontend to track the worktime.
 */
function pwlHandleWorktimePing()
{
    if (is_user_logged_in() && isset($_POST['currentPostId']) && isset($_POST['worktimeToAdd']))
    {
        $postId = $_POST['currentPostId'];
        $worktimeToAdd = $_POST['worktimeToAdd'];

        if (is_numeric($postId) && is_numeric($worktimeToAdd))
        {
            $post = get_post($postId);

            if ($post)
            {
                $oldWorktime = get_post_meta($postId, "post-worktime", true);
                if ($oldWorktime===false) $oldWorktime = 0;

                $newWorktime = $oldWorktime+$worktimeToAdd;

                update_post_meta($postId, "post-worktime", $newWorktime);
            }
        }
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
    else $content = __("You have to save the post, before we can track the time.", PWL_TEXT_DOMAIN);


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
    $content .= __('Current worktime', PWL_NAME).': <span id="frontendTime">0</span><br />';
    $content .= __("Total worktime", PWL_NAME).': <span id="serverWorktime">';
    $content .= pwlSecondsToHumanReadableTime($_totalWorktime);
    $content .= '</span><br />';

    if (isControlBoxEnabled())
    {
        $content .= '<button class="button button-small pwl-button" id="pwl-pause-button">'.__("Pause", PWL_TEXT_DOMAIN).'</button>';
        $content .= '<button class="button button-small pwl-button" style="display:none;" id="pwl-resume-button">'.__("Resume", PWL_TEXT_DOMAIN).'</button>';
        $content .= '<button class="button button-small pwl-button" id="pwl-reset-button">'.__("Reset", PWL_TEXT_DOMAIN).'</button>';
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
        __( 'Post Worktime', PWL_TEXT_DOMAIN),
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
        array('pwlworktimecolumn' => __('Worktime', PWL_TEXT_DOMAIN)) );
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
    $domain = PWL_TEXT_DOMAIN;
    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
    if ( $loaded = load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' ) )
    {
        return $loaded;
    }
    else
    {
        load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
    }
});

//Register admin javascript file
add_action("admin_enqueue_scripts", function ($hook) {
    if ($hook=="toplevel_page_post-worktime-logger-statistics"){
		wp_enqueue_script(PWL_NAME, plugins_url( "resources/js/Chart.bundle.min.js", __FILE__ ));
	}

    global $pwlOptions;

    wp_enqueue_style(PWL_NAME, plugins_url( "resources/css/post-worktime-logger.css", __FILE__ ));

	if ($hook=="post.php")
	{
		wp_enqueue_script(PWL_NAME, plugins_url( "resources/js/post-worktime-logger.js", __FILE__ ));

        wp_localize_script( PWL_NAME, 'pwl', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'inactivityTimeout' => ( ! empty( $pwlOptions['inactivityTimeout'] ) ) ? esc_html( $pwlOptions['inactivityTimeout'] ) : '5',
        ) );
	}
});

//Register scripts for frontend
add_action("wp_enqueue_scripts", function () {
    wp_enqueue_style(PWL_NAME, plugins_url( "resources/css/post-worktime-logger.css", __FILE__ ));

    if (is_user_logged_in())
    {//only include js if the user is logged in.
        wp_enqueue_script(PWL_NAME, plugins_url( "resources/js/post-worktime-logger.js", __FILE__ ), array("jquery"));
        wp_localize_script( PWL_NAME, 'pwl',
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
