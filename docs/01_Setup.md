To use the Bundle, you can use the following command:

```bash
composer require "biblioverse/typesense-bundle"
```

Note that as long as the repo is not ready, you will need to include the repository in your `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/biblioverse/TypesenseBundle"
    }
  ]
}
```
Then you will need to add the bundle to your `config/bundles.php`:

```php
<?php
$bundles = [
    // ...
    Biblioverse\TypesenseBundle::class => ['all' => true],
];
```

And finally, you will need to add the configuration to your `config/packages/biblioverse_typesense.yaml`:



```yaml
parameters:
  env(TYPESENSE_URL): 'http://typesense:8108'
  env(TYPESENSE_KEY): 'mykey'
biblioverse_typesense:
  typesense:
    uri: '%env(TYPESENSE_URL)%'
    key: '%env(TYPESENSE_KEY)%'

when@test:
  biblioverse_typesense:
    auto_update: false
```

This is the minimum configuration needed to use the bundle.

By setting `auto_update` to `false` on `test` environment, you make sure that typesense will not update your typesense document when you flush an entity.
