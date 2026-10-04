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

Sign up (with `flag('enable_signup', true)` in `config/config.php`) can be restricted to invitation codes by listing them in `config/codes.txt`, one per line. Invitation links can prefill the code: `/auth/signup?code=...`
