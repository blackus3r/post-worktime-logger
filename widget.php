<?php
/**
 *
 * @copyright Patrick Hausmann
 * @author Patrick Hausmann <privat@patrck-designs.de>
 */

/**
 * This is a frontend widget to display the worktime in frontend.
 * This provides functionalities to allow logged in users to pause, resume and reset the worktime.
 */
class PwlFrontendWidget extends WP_Widget
{
    const PWL_TEXT_DOMAIN = "post-worktime-logger";

    public function __construct()
    {
        parent::__construct(
            'pwl_frontend_widget',
            'Post Worktime Logger',
            array(
                'description' => __("A widget in the frontend to display the admin worktime widget for logged in users or the worktime for the current post for not logged in users.", self::PWL_TEXT_DOMAIN)
            )
        );
    }

    public function widget( $_args, $_instance )
    {
        if (!is_front_page())
        {
            $queriedObject = get_queried_object();
            if ($queriedObject)
            {
                $currentPostId = $queriedObject->ID;

                if ($currentPostId)
                {
                    $title = apply_filters('widget_title', $_instance['title']);
                    $displayWorktimeForNotLoggedInUsers = $_instance['displayWorktimeForNotLoggedInUsers'] ? true : false;
                    $disableFrontendTimeTracking = $_instance['disableFrontendTimeTracking'] ? true : false;
                    $preText = $_instance['preText'];
                    $afterText = $_instance['afterText'];
                    $content = "";
                    $worktime = get_post_meta($currentPostId, "post-worktime", true);
                    if (!$worktime)
                    {
                        update_post_meta( $currentPostId, "post-worktime", 0 );
                        $worktime = $worktime = get_post_meta($currentPostId, "post-worktime", true);
                    }

                    $content.= $_args['before_widget'];
                    if (!empty($title))
                    {
                        $content.=  $_args['before_title'] . $title. $_args['after_title'];
                    }

                    if (is_user_logged_in() && $disableFrontendTimeTracking!=true)
                    {//display the controllbox in frontend
                        $content.= pwlGetPostWorktimeLoggerControlBox($worktime, $currentPostId);
                    }
                    else
                    {
                        $content .= $preText.pwlSecondsToHumanReadableTime($worktime).$afterText;
                    }

                    $content.=  $_args['after_widget'];

                    if (is_user_logged_in() || (!is_user_logged_in() && $displayWorktimeForNotLoggedInUsers))
                    {//We have a logged in user, or a non logged in user, but 'displayWorktimeForNotLoggedInUsers' is enabled.
                        echo $content;
                    }
                }
            }
        }
    }

    public function form( $_instance )
    {
        $defaults = array(
            'title' => '',
            'displayWorktimeForNotLoggedInUsers' => false,
            'preText' => '',
            'afterText' => ''
        );
        $_instance = wp_parse_args((array)$_instance, $defaults);

        $title = $_instance['title'];
        $displayWorktimeForNotLoggedInUsers = isset($_instance['displayWorktimeForNotLoggedInUsers']) ? $_instance['displayWorktimeForNotLoggedInUsers'] : false;;
        $disableFrontendTimeTracking = isset($_instance['disableFrontendTimeTracking']) ? $_instance['disableFrontendTimeTracking'] : false;
        $preText = $_instance['preText'];
        $afterText = $_instance['afterText'];
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php echo __('Title:', self::PWL_TEXT_DOMAIN); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('displayWorktimeForNotLoggedInUsers'); ?>"><?php echo __("Display widget for not logged in users", self::PWL_TEXT_DOMAIN); ?></label>
            <input class="checkbox" id="<?php echo $this->get_field_id('displayWorktimeForNotLoggedInUsers'); ?>" name="<?php echo $this->get_field_name('displayWorktimeForNotLoggedInUsers'); ?>" type="checkbox" <?php checked($displayWorktimeForNotLoggedInUsers, 'on' ); ?>/>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('disableFrontendTimeTracking'); ?>"><?php echo __("Disable frontend time tracking", self::PWL_TEXT_DOMAIN); ?></label>
            <input class="checkbox" id="<?php echo $this->get_field_id('disableFrontendTimeTracking'); ?>" name="<?php echo $this->get_field_name('disableFrontendTimeTracking'); ?>" type="checkbox" <?php checked($disableFrontendTimeTracking, 'on' ); ?>/>
            <p><?php echo __("This will only affect logged in users.", self::PWL_TEXT_DOMAIN) ?></p>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('preText'); ?>"><?php echo __("Pre text:", self::PWL_TEXT_DOMAIN); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('preText'); ?>" type="text" value="<?php echo esc_attr($preText); ?>" />
            <p><?php echo __("This will only appear if 'Display widget for not logged in users' is enabled.", self::PWL_TEXT_DOMAIN) ?></p>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('afterText'); ?>"><?php echo __("After text:", self::PWL_TEXT_DOMAIN); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('afterText'); ?>" type="text" value="<?php echo esc_attr($afterText); ?>" />
            <p><?php echo __("This will only appear if 'Display widget for not logged in users' is enabled.", self::PWL_TEXT_DOMAIN) ?></p>
        </p>
        <?php
    }

    public function update( $_newInstance, $_oldInstance )
    {
        $instance = array();

        $instance['title'] = strip_tags($_newInstance['title']);
        $instance['displayWorktimeForNotLoggedInUsers'] = $_newInstance['displayWorktimeForNotLoggedInUsers'];
        $instance['disableFrontendTimeTracking'] = $_newInstance['disableFrontendTimeTracking'];
        $instance['preText'] = $_newInstance['preText'];
        $instance['afterText'] = $_newInstance['afterText'];

        return $instance;
    }
}
