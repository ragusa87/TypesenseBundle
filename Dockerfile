FROM php:8.2-cli
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN apt-get update && apt-get install -y git unzip && rm -Rf /var/lib/apt/lists/*

RUN mkdir -p  /.composer/cache/ && chown -R 1000:1000 /.composer/cache/
USER 1000:1000