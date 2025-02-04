
You can fill your Typesense collection with your entities by using the populate command

```php
bin/console biblioverse:typesense:populate
```

This will populate your collection with all the entities from your configuration.
If you only want to populate the mapping without data, use the `--no-data` option.

The command will wait for the Typesense server and the database to be ready before starting the population.

It will try to check the connectivity every second for `--nb-retry` times.