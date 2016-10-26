[![Codacy Badge](https://api.codacy.com/project/badge/Grade/ec1ee9f6188548b1b2694e7ce3298399)](https://www.codacy.com/app/patrick-hausmann/post-worktime-logger?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=blackus3r/post-worktime-logger&amp;utm_campaign=Badge_Grade)
[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/blackus3r/post-worktime-logger.svg)](http://isitmaintained.com/project/blackus3r/post-worktime-logger "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/blackus3r/post-worktime-logger.svg)](http://isitmaintained.com/project/blackus3r/post-worktime-logger "Percentage of issues still open")

# Post Worktime Logger

Post Worktime Logger is a tool to track the time you worked on each post.

This is a wordpress plugin. https://wordpress.org/plugins/post-worktime-logger/

## Suggest new features

There is a missing feature? Please create an issue for that.

## Contributing to Post Worktime Logger

First off, thanks for your desire to contribute! This project follows the WordPress Coding Standards. You can install PHPCS and the WPCS globally, or you can install them within this project.

To install them in this project, run the following

1. `composer install` to install PHPCS and WPCS
2. `vendor/bin/phpcs --config-set installed_paths ../../wp-coding-standards/` to add WPCS to the project version of PHPCS

With those commands run, you should be able to run PHPCS against any file or directory to show which (if any) lines of code are violating the standard. You can also run PHPCBF against a file or directory to auto-fix applicable errors.

To check a file or directory, run the following command:

1. `vendor/bin/phpcs --standard=ruleset.xml /path/to/file.php` to report the errors
2. `vendor/bin/phpcbf --standard=ruleset.xml /path/to/file.php` to auto-fix errors

## Report a bug

If you have found a bug, please create an issue or feel free to fix it and make a pull request for that fix.

