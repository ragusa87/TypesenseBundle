set shell := ["docker", "compose", "run", "--entrypoint", "/bin/sh", "-it", "--user=1000", "--rm", "php", "-c"]

os_open := if os() == 'windows' {
  "start"
} else if os() == 'macos' {
  "open"
} else {
  "xdg-open"
}


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
    composer rector

test-phpcs:
    composer run test-phpcs

phpcs:
    composer run phpcs

phpunit *args="":
    env XDEBUG_MODE=coverage composer run phpunit -- {{args}}

phpunit-xdebug *args="":
    composer phpunit-xdebug -- {{args}}

init-db *args="":
    composer run init-db -- {{args}}

phpstan *args="":
    composer phpstan -- {{args}}

phpunit-open-report:
    #!/usr/bin/bash
    {{ os_open }} {{invocation_directory()}}/tests/coverage/html-coverage/index.html
changelog:
    #!/usr/bin/bash
    rm -f changelog.yml
    chglog config
    chglog init
    chglog format --template repo -c .chlog.yml > CHANGELOG.md
