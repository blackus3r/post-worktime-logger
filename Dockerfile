FROM wordpress:4.6.1-apache

MAINTAINER selamanse <selamanse@scheinfrei.info>

RUN mkdir /usr/src/wordpress/wp-content/plugins/post-worktime-logger \
    && mkdir /usr/src/wordpress/wp-content/plugins/post-worktime-logger/lang \
    && mkdir /usr/src/wordpress/wp-content/plugins/post-worktime-logger/resources

COPY readme.txt /usr/src/wordpress/wp-content/plugins/post-worktime-logger/
COPY *.php /usr/src/wordpress/wp-content/plugins/post-worktime-logger/
COPY lang /usr/src/wordpress/wp-content/plugins/post-worktime-logger/lang
COPY resources /usr/src/wordpress/wp-content/plugins/post-worktime-logger/resources

RUN chown -R www-data:www-data /usr/src/wordpress/wp-content/plugins/post-worktime-logger
