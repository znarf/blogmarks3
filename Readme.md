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

Sign up (with `flag('enable_signup', true)` in `config/config.php`) can be restricted to invitation codes with `flag('signup_codes', ['alpha', 'beta'])`. Invitation links can prefill the code: `/auth/signup?code=...`

When codes are used, the code is stored on the user, which needs the `config/migrations/2026-10-04-users-code.sql` migration.
