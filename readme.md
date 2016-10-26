[![Codacy Badge](https://api.codacy.com/project/badge/Grade/ec1ee9f6188548b1b2694e7ce3298399)](https://www.codacy.com/app/patrick-hausmann/post-worktime-logger?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=blackus3r/post-worktime-logger&amp;utm_campaign=Badge_Grade)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/blackus3r/post-worktime-logger.svg)](http://isitmaintained.com/project/blackus3r/post-worktime-logger "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/blackus3r/post-worktime-logger.svg)](http://isitmaintained.com/project/blackus3r/post-worktime-logger "Percentage of issues still open")

# Post Worktime Logger

Post Worktime Logger is a WordPress plugin that allows you to track the time you worked on each post.

You can find more information here. https://wordpress.org/plugins/post-worktime-logger/

## What does it do?

Post Worktime Logger will tell you how much time did you spend in a WordPress post. It will only count the time if you are actively working in the post, so don't worry if you have to leave the computer alone!

## Installation

1. Clone this repository and switch to the branch you want to use.
2. Upload /post-worktime-logger/ to the /wp-content/plugins/ directory in your WordPress blog.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage

Post Worktime Logger will start to track the time you spend in each post automatically as soon as you install it. You'll find the time in various places, such as the meta box when you're editing a page, or the posts list.

In the plugin settings you can edit the title of the plugin, and enable or disable if you want to let non-logged users to see it.

## Screenshots

![meta_box](screenshots/screenshot-1.png)
![list](screenshots/screenshot-2.png)
![statistics](screenshots/screenshot-6.png)
![control_panel](screenshots/screenshot-4.png)

## Suggest new features

There is a missing feature? Please create an issue for that.

## Report a bug

If you have found a bug, please create an issue or feel free to fix it and make a pull request for that fix.

## Changelog

###1.2.2
* Fixed a bug, that no worktime was saved anymore in admin area.

###1.2.1
* Added language pr_BR
* Fixed typos
* Fixed loading text languages
* Added notice that this plugin is maintained on Github.

###1.2.0
* Implemented a frontend widget to display worktime for not logged in users and for logged in users the control box to track the worktime.
* Improved security for the plugin.
* Made the code more reuseable.
* Fixed textdomain for strings.
* Updated german translation.

###1.1.0
* Implemented support to pause and resume the tracking of the work time.
* Implemented support to reset the working time of a post.
* Implemented support for a sortable column on posts page, which displays the total worktime for each post.
* Added languages fr_Be and sq.

###1.0.0
* First Version

## License

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation. Using the GPLv3 License. More info here. https://www.gnu.org/licenses/gpl.html
