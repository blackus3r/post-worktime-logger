<?php
/**
 *
 * @copyright Patrick Hausmann
 * @author Patrick Hausmann <privat@patrck-designs.de>
 */

/**
 * PostWorktimeLoggerSettingsPage
 *
 */
class PostWorktimeLoggerSettingsPage
{
    const PWL_TEXT_DOMAIN = "post-worktime-logger";
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        $this->options = get_option("post-worktime-logger-options");

        add_action( 'admin_notices', array( $this, 'pwlResetNotice' ) );
        add_action('admin_menu', array($this, 'registerSettingsPage'));
        add_action('admin_init', array($this, 'pageInit'));
        add_action('admin_action_pwlResetWholeWorktime', array($this, "resetWholeWorktime"));
    }

    /**
     * Prints admin notice, when reset whole worktime was pressed.
     */
    public function pwlResetNotice()
    {
        if ( ! isset( $_GET['pwlResetPostsNumber'] ) ) {
            return;
        }
        ?>
        <div class="updated">
            <p><?php printf(__( 'Resetted worktime for %s posts.', self::PWL_TEXT_DOMAIN ), $_GET['pwlResetPostsNumber']); ?></p>
        </div>
        <?php
    }

    /**
     * Resets whole worktime.
     */
    function resetWholeWorktime()
    {
        $updatedPosts = 0;
        if (is_user_logged_in() && current_user_can("manage_options"))
        {
            $args = array(
                'posts_per_page'   => -1
            );

            foreach(get_posts($args) as $post)
            {
                if (delete_post_meta($post->ID, 'post-worktime', 1));
                {
                    $updatedPosts++;
                }
            }
        }

        wp_redirect(add_query_arg( array( 'pwlResetPostsNumber' =>  $updatedPosts), $_SERVER['HTTP_REFERER'] ));
        exit();
    }

    /**
     * Add options page
     */
    public function registerSettingsPage()
    {
        add_menu_page(
            __("Statistics", self::PWL_TEXT_DOMAIN),
            __("Worktime Logger", self::PWL_TEXT_DOMAIN),
            'manage_options',
            "post-worktime-logger-statistics",
            array($this, "createAdminStatisticsPage")
        );

        add_submenu_page(
            "post-worktime-logger-statistics",
            __("Settings", self::PWL_TEXT_DOMAIN),
            __("Settings", self::PWL_TEXT_DOMAIN),
            'manage_options',
            'post-worktime-logger-settings',
            array($this, "createAdminSettingsPage")
        );
    }

    /**
     * Prints a statistic page.
     */
    function createAdminStatisticsPage()
    {
        if ( !current_user_can( 'manage_options' ) )  {
            wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        $numOfPosts = 25;

        $args = array(
            'posts_per_page' => $numOfPosts,
            'order'		=> 'DESC',
            'orderby'   => 'meta_value_num',
            'meta_key'  => 'post-worktime',
        );

        $query = new WP_Query( $args );

        $posts_titles = array();
        $posts_worktimes = array();

        if ( $query->have_posts() ) {

            $tPosts = $query->get_posts();

            foreach($tPosts as $tPost){
                $posts_titles[] = $tPost->post_title;
                $posts_worktimes[] = round((get_post_meta($tPost->ID, "post-worktime", true)/60), 0);
            }

            echo '<div class="wrap">';
            echo "<h1>".__("Statistics", self::PWL_TEXT_DOMAIN)."</h1>";
            echo '<h2>'.sprintf(__("Top %s posts (worktime)", self::PWL_TEXT_DOMAIN), $numOfPosts).'</h2>';
            echo '<div id="chartsContainer" style="width:90%;">';
            echo '<canvas id="pwlTopWorktimePosts" width="400" height="200"></canvas>';
            echo '</div>';
            echo '</div>';

            echo "<script type='text/javascript'>
				jQuery(document).ready(function () {

                    var ctx = document.getElementById('pwlTopWorktimePosts');
                    var pwlTopFiveWorktimePosts = new Chart(ctx, {
                        type: 'horizontalBar',
                        data: {
                            labels: " . (json_encode($posts_titles,JSON_HEX_QUOT)) . ",
                            datasets: [{
                                label: '".__('Minutes', self::PWL_TEXT_DOMAIN)."',
                                generateLabels: null,
                                data: [" . implode(',', $posts_worktimes) . "],
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                hoverBackgroundColor: 'rgba(54, 162, 235, 0.5)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                        display: true,
                        barThickness: 2,
                            scales: {
                                yAxes: [{
                                    ticks: {
                                        beginAtZero:true
                                    }
                                }]
                            }
                        }
                    });
                });
			</script>";

            return;
        }
        else _e('No data.', self::PWL_TEXT_DOMAIN);
    }

    /**
     * Options page callback
     */
    public function createAdminSettingsPage()
    {
        ?>
        <div class="wrap">
            <h1><?php echo __("Post Worktime Logger Settings", self::PWL_TEXT_DOMAIN); ?></h1>
            <form class="pwl-reset-form" method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('post-worktime-logger-option-group');
                do_settings_sections('post-worktime-logger-settings');
                submit_button(__("Save Changes"), "primary", "submit", false);
                ?>
            </form>
            <form class="pwl-reset-form" method="post" action="<?php echo admin_url( 'admin.php' ); ?>">
                <button name="action" value="pwlResetWholeWorktime" class="button danger"><?php _e("Reset whole worktime", self::PWL_TEXT_DOMAIN); ?></button>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function pageInit()
    {
        register_setting(
            'post-worktime-logger-option-group', // Option group
            'post-worktime-logger-options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'general', // ID
            __('General', self::PWL_TEXT_DOMAIN),
            null, // Callback
            'post-worktime-logger-settings' // Page
        );

        add_settings_field(
           'enableControlButtons',
            __('Enable control buttons', self::PWL_TEXT_DOMAIN),
            array( $this, 'enableControlButtonsCallback'),
            'post-worktime-logger-settings',
            'general'
        );

        add_settings_field(
           'disableAutoStart',
            __('Disable Auto Start', "post-worktime-logger"),
            array( $this, 'disableAutoStartCallback'),
            'post-worktime-logger-settings',
            'general'
        );

        add_settings_field(
           'inactivityTimeout',
            __('Inactivity Timeout', self::PWL_TEXT_DOMAIN),
            array( $this, 'inactivityTimeoutCallback'),
            'post-worktime-logger-settings',
            'general'
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $_input Contains all settings fields as array keys
     *
     * @return array
     */
    public function sanitize($_input)
    {
        $newInput = array();
        if( isset( $_input['enableControlButtons'] ) )
        {
            $newInput['enableControlButtons'] = $_input['enableControlButtons'];
        }

        if( isset( $_input['disableAutoStart'] ) )
        {
            $disableAutoStart = sanitize_text_field( wp_unslash( $_input['disableAutoStart'] ) );
            $newInput['disableAutoStart'] = $disableAutoStart;
        }

        if( isset( $_input['inactivityTimeout'] ) )
        {
            $inactivityTimeout = sanitize_text_field( wp_unslash( $_input['inactivityTimeout'] ) );

            if ( is_numeric( $inactivityTimeout ) ) {
                $newInput['inactivityTimeout'] = $inactivityTimeout;
            }
        }

        return $newInput;
    }

    /**
 * Get the settings option array and print one of its values
 */
    public function enableControlButtonsCallback()
    {
        if (isset($this->options['enableControlButtons']))
        {
            $enableControlButtons = $this->options['enableControlButtons'];
        }
        else $enableControlButtons = null;

        ?>
        <input type="checkbox" id="enableControlButtons" name="post-worktime-logger-options[enableControlButtons]"  <?php checked($enableControlButtons, 'on' ); ?> />
        <p class="description"><?php esc_html_e( "This will allow you to pause, resume and reset the worktime.", self::PWL_TEXT_DOMAIN ); ?></p>
        <?php
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function disableAutoStartCallback()
    {
        if (isset($this->options['disableAutoStart']))
        {
            $disableAutoStart = $this->options['disableAutoStart'];
        }
        else $disableAutoStart = null;

        ?>
        <input type="checkbox" id="disableAutoStart" name="post-worktime-logger-options[disableAutoStart]"  <?php checked($disableAutoStart, 'on' ); ?> />
        <p class="description">
            <?php esc_html_e( "Activate this to prevent the time from starting automatically on post editing.", "post-worktime-logger" ); ?>
        </p>
        <?php
    }

    /**
     * Display the HTML for the minutes of inactivity option.
     */
    public function inactivityTimeoutCallback()
    {
        $inactivityTimeout = 5;

        if (! empty($this->options['inactivityTimeout']))
        {
            $inactivityTimeout = $this->options['inactivityTimeout'];
        }

        ?>
        <input type="text" size="3" id="inactivityTimeout" name="post-worktime-logger-options[inactivityTimeout]"  value="<?php echo esc_html( $inactivityTimeout ); ?>" />
        <p class="description"><?php esc_html_e( "This option allows you to specify a certain number of minutes that can pass without activity before the timer pauses.", self::PWL_TEXT_DOMAIN); ?></p>
        <?php
    }
}
