set shell := ["docker", "compose", "run", "-it", "--user=1000", "--rm", "php", "/usr/bin/composer"]
composer *args:
    {{args}}

install:
     install

tests:
     test
update:
    update
lint:
     lint

rector:
     rector