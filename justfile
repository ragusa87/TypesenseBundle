set shell := ["docker", "compose", "run", "--entrypoint", "/bin/sh", "-it", "--user=1000", "--rm", "php", "-c"]
composer *args="":
    /usr/bin/composer {{args}}

sh *args="":
    sh {{args}}

php *args="":
    php {{args}}

install:
    composer install

tests:
    composer run test

update *args="":
    composer update {{args}}

lint:
    composer run lint

rector:
    rector

test-phpcs:
    composer run test-phpcs

phpcs:
    composer run phpcs

phpunit *args="":
    env XDEBUG_MODE=coverage composer run phpunit -- {{args}}

phpunit-xdebug *args="":
    composer phpunit-xdebug -- {{args}}

phpstan *args="":
    composer phpstan -- {{args}}