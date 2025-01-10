
You can fill your Typesense collection with your entities by using the populate command

```php
bin/console biblioteca:typesense:populate
```

This will populate your collection with all the entities from your configuration.
If you only want to populate the mapping without data, use the `--no-data` option.
