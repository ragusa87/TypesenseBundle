To use the Bundle, you can use the following command:

```bash
composer require "Biblioteca/TypesenseBundle"
```
Then you will need to add the bundle to your `config/bundles.php`:

```php
<?php
$bundles = [
    // ...
    Biblioteca\TypesenseBundle::class => ['all' => true],
];
```

And finally, you will need to add the configuration to your `config/packages/biblioteca_typesense.yaml`:



```yaml
parameters:
  env(TYPESENSE_URL): 'http://typesense:8108'
  env(TYPESENSE_KEY): 'mykey'
biblioteca_typesense:
  typesense:
    uri: '%env(TYPESENSE_URL)%'
    key: '%env(TYPESENSE_KEY)%'
```

This is the minimum configuration needed to use the bundle.