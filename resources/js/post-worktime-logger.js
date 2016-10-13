/**
 *
 * @copyright Patrick Hausmann
 * @author Patrick Hausmann <privat@patrck-designs.de>
 */

var currentPostId;
var frontendTimeContainer;
var frontendTime = 0;
var frontendTimeBuffer = 0;
var lastMouseMove;
var frontendWorktTimeTextAreaField;
var playButton;
var pauseButton;
var enablePause = false;

jQuery(document).ready(function () {

    /**
     * Converts the given seconds to human readable time.
     *
     * @param {int} seconds the seconds to convert.
     * @returns {string} the hum readable string.
     */
    function pwlSecondsToString(seconds)
    {
        var numhours = Math.floor(seconds / 3600);
        var numminutes = Math.floor((seconds % 3600) / 60);
        var numseconds = (seconds % 3600) % 60;


        if (numhours<10) numhours ="0"+numhours;
        if (numminutes<10) numminutes ="0"+numminutes;
        if (numseconds<10) numseconds ="0"+numseconds;

        return numhours + ":" + numminutes + ":" + numseconds;
    }

    /**
     * Sends a ping to the backend, to track the time.
     */
    function pwlSendPing()
    {
        if (pwlCheckActivity())
        {
            pwlUpdateCustomFieldTextBox();
            jQuery.post(
                ajaxurl,
                {
                    "currentPostId": currentPostId,
                    "action": "worktime_ping"
                }
            );
        }
    }

    /**
     * Updates te frontend custom field textarea to prevent overwriting the value on post save.
     */
    function pwlUpdateCustomFieldTextBox()
    {
        var oldWorkTime = parseInt(frontendWorktTimeTextAreaField.val(), 10);
        frontendWorktTimeTextAreaField.val((oldWorkTime+frontendTimeBuffer));
        frontendTimeBuffer=0;
    }

    /**
     * Checks if the user has done something in the last 5 minutes.
     */
    function pwlCheckActivity()
    {
        var currentTime = new Date;
        var lastActivity = Math.round((currentTime.getTime() - lastMouseMove.getTime())/1000);

        if (lastActivity<60*5 && !enablePause)
        {
            return true;
        }
        else return false;
    }

    /**
     * Updates the frontend timer.
     */
    function pwlWorktime()
    {
        if (pwlCheckActivity())
        {
            frontendTime++;
            frontendTimeBuffer++;
            frontendTimeContainer.html(pwlSecondsToString(frontendTime));
        }
    }

    currentPostId = jQuery("#post-worktime-logger-current-post-id").html();
    frontendTimeContainer = jQuery("#frontendTime");
    frontendWorktTimeTextAreaField = jQuery('input[value="post-worktime"]').parent().parent().find("textarea");
    frontendTime = frontendTimeContainer.html();

    playButton = jQuery("#pwl-play-button");
    pauseButton = jQuery("#pwl-pause-button");

    playButton.click(function (_event) {
        _event.preventDefault();
        enablePause = false;
        playButton.toggle();
        pauseButton.toggle();
    });

    pauseButton.click(function (_event) {
        _event.preventDefault();
        enablePause = true;
        pauseButton.toggle();
        playButton.toggle();
    });

    lastMouseMove = new Date();
    jQuery(document).mousemove(function() {
        lastMouseMove = new Date();
    });

    if (currentPostId) setInterval(pwlSendPing, 10000);
    if (frontendTimeContainer) setInterval(pwlWorktime, 1000);
});