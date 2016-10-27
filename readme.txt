=== Post Worktime Logger===
Contributors: filme-blog
Donate link:
Tags: worktime, work, clock, time, time-tracking, tracking, Zeiterfassung, worktime logger, post work time, working time
Requires at least: 2.3.1
Tested up to: 4.6
Stable tag: 1.3.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl.html
German changelog and tutorials of ths plugin can be found on [DerPade](http://www.derpade.de/series/post-worktime-logger/)

Post Worktime Logger is a WordPress plugin that allows you to track the time you worked on each post.

== Description ==

Do you ever wanted to know, how long did you worked on this post?
Let Post Worktime Logger fill this gap!
Post Worktime Logger will tell you how much time did you spend in a WordPress post. It will only count the time if you are actively working in the post, so don't worry if you have to leave the computer alone!

This project is actively maintained on [Github](https://github.com/blackus3r/post-worktime-logger)

== Installation ==

1. Upload `/post-worktime-logger/` to the `/wp-content/plugins/` directory in your WordPress blog.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Post Worktime Logger is now tracking your working tim for each post.

== Usage ==

Post Worktime Logger will start to track the time you spend in each post automatically as soon as you install it. You'll find the time in various places, such as the meta box when you're editing a page, or the posts list.

In the plugin settings you can edit the title of the plugin, and enable or disable if you want to let non-logged users to see it.

== Screenshots ==

1. This is the meta box right to the post editor.
1. This is the posts page.
1. These are the widget options.
1. This is the control panel in frontend for logged in users.
1. This is the widget in frontend for not logged in users.
1. This is the statistics page which displays the top 25 posts.
1. This is the settings page.

== Changelog ==

= 1.3.0 =
* Implemented an own page for Post Worktime Logger.
* Implemented an options page with some nice options and a reset button.
* Implemented a statistics page to show the top 25 posts (worktime.
* Improved documentation of the plugin.

= 1.2.3 =
* Refactored the calculation of the worktime. This should be now more precise.

= 1.2.2 =
* Fixed a bug, that no worktime was saved anymore in admin area.

= 1.2.1 =
* Added language pr_BR
* Fixed typos
* Fixed loading text languages
* Added notice that this plugin is maintained on Github.

= 1.2.0 =
* Implemented a frontend widget to display worktime for not logged in users and for logged in users the control box to track the worktime.
* Improved security for the plugin.
* Made the code more reuseable.
* Fixed textdomain for strings.
* Updated german translation.

= 1.1.0 =
* Implemented support to pause and resume the tracking of the work time.
* Implemented support to reset the working time of a post.
* Implemented support for a sortable column on posts page, which displays the total worktime for each post.
* Added languages fr_Be and sq.

= 1.0.0 =
* First Version
