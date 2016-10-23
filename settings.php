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

        add_action( 'admin_menu', array($this, 'registerSettingsPage' ));
        add_action( 'admin_init', array( $this, 'pageInit') );
    }

    /**
     * Add options page
     */
    public function registerSettingsPage()
    {
        add_menu_page(
            __("Statistics ", "post-worktime-logger"),
            __("Worktime Logger", "post-worktime-logger"),
            'manage_options',
            "post-worktime-logger-statistics",
            array($this, "createAdminStatisticsPage")
        );

        add_submenu_page(
            "post-worktime-logger-statistics",
            __("Settings", "post-worktime-logger"),
            __("Settings", "post-worktime-logger"),
            'manage_options',
            'post-worktime-logger-settings',
            array($this, "createAdminSettingsPage")
        );
    }

    /**
     *
     */
    public function createAdminStatisticsPage()
    {
        echo "<h1>Statistics</h1>";
        echo "<p>Comming soon...</p>";
    }

    /**
     * Options page callback
     */
    public function createAdminSettingsPage()
    {
        ?>
        <div class="wrap">
            <h1><?php echo __("Post Worktime Logger Settings", "post-worktime-logger"); ?></h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('post-worktime-logger-option-group');
                do_settings_sections('post-worktime-logger-settings');
                submit_button(__("Save Changes"), "primary", "submit", false);

                /**
                 * Todo: implement reset button.
                 * <button name="resetWholeWorktime" value="true" class="button danger"><?php _e("Reset whole worktime", "post-worktime-logger"); ?></button>
                 */
                ?>
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
            __('General', "post-worktime-logger"),
            null, // Callback
            'post-worktime-logger-settings' // Page
        );

        add_settings_field(
           'enableControlButtons',
            __('Enable control buttons', "post-worktime-logger"),
            array( $this, 'enableControlButtonsCallback'),
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
        <?php
    }
}