# Blogmarks 3

## Get Started

    git@github.com:znarf/blogmarks3.git
    cd blogmarks3

    curl -s https://getcomposer.org/installer | php
    php composer.phar install

Optional:

    npm install -g bower
    bower install

Run:

    php -S localhost:8002 -t public

Screenshots (via [Microlink](https://microlink.io), 25 free requests per day):

    php workers/screenshot.worker.php

Run it from cron (e.g. hourly). Pass a user login to also queue their existing marks.
