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

    private $optionsPrefix = "pwl";

    /**
     * Start up
     */
    public function __construct()
    {
        $this->options = get_option("post-worktime-logger-options");

        add_action( 'admin_menu', array( $this, 'addPluginPage') );
        add_action( 'admin_init', array( $this, 'pageInit') );
    }

    /**
     * Add options page
     */
    public function addPluginPage()
    {
        // This page will be under "Settings"
        add_options_page(
            __('Post WorkTime Logger Settings', "post-worktime-logger"),
            __('Post Worktime Logger', "post-worktime-logger"),
            'manage_options',
            'post-worktime-logger-settings',
            array( $this, 'createAdminPage' )
        );
    }

    /**
     * Options page callback
     */
    public function createAdminPage()
    {
        ?>
        <div class="wrap">
            <h1><?php echo __("Post Worktime Logger Settings", "post-worktime-logger"); ?></h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('post-worktime-logger-option-group');
                do_settings_sections('post-worktime-logger-settings');
                submit_button();
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
           'showWorktimeInPostMeta',
            __('Show worktime in post meta', "post-worktime-logger"),
            array( $this, 'showWorktimeInPostMetaCallback'),
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
    public function sanitize($_input )
    {
        $newInput = array();
        if( isset( $_input['showWorktimeInPostMeta'] ) )
        {
            $newInput['showWorktimeInPostMeta'] = $_input['showWorktimeInPostMeta'];

        }

        return $newInput;
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function showWorktimeInPostMetaCallback()
    {

        if (isset($this->options['showWorktimeInPostMeta']))
        {
            $showWorktimeInPostMeta = $this->options['showWorktimeInPostMeta'];
        }
        else $showWorktimeInPostMeta = null;

        ?>
            <input type="checkbox" id="showWorktimeInPostMeta" name="post-worktime-logger-options[showWorktimeInPostMeta]"  <?php checked($showWorktimeInPostMeta, 'on' ); ?> />
        <?php
    }
}