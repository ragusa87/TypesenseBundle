To use Entities mapping, you will need to configure the mapping between your entities and the Typesense collections.

You can do that in the configuration. See the example below.

```yaml
biblioverse_typesense:
  typesense:
    uri: '%env(TYPESENSE_URL)%'
    key: '%env(TYPESENSE_KEY)%'
  collections:
    houses:
      entity: 'App\Entity\House'
      mapping:
        fields:
          - name: name
            type: string
            sort: true
            facet: true
          - name: owners
            type: string[]

```

Configuring the mapping involves the following functionalities:
* Your entity class is indexed on every insertion/update/deletion.
* You can automatically populate your entities into typesense.
* A service is automatically available to interact with the Typesense API and hydrate the result with your entities.

Limitation:
* Only entities with a Single Identifier are supported. PR are welcome to support composite keys.



# Types

The type such as `string` or `string[]` are the same as the Typesense types. You can see the full list of types in the [Typesense documentation](https://typesense.org/docs/0.21.0/api/collections.html#schema-fields).

You can also use the following enum: `Biblioverse\TypesenseBundle\Type\DataTypeEnum`.

# Entity attribute
If the field name in the entity is not matching the one on the Typesense collection, you can use the `entity_attribute` configuration to map the entity field to the collection field.

```php
class House
{
  // ...
  public function getTypesenseAddress(): string
  {
    return implode(', ', [$this->street, $this->city, $this->country]);
  }
}
```

Config
```yaml
    ...
    mapping:
      fields:
        - name: address
          type: string
          entity_attribute: typesenseAddress
```

# Auto embedding fields

You can use the Typesense auto embedding fields to generate embeddings. 

```yaml
  embedding:
    name: embedding
    type: float[]
    index: true
    embed:
      from: ["field_to_embed"]
      model_config:
        model_name: "..."
        api_key: '...'
        url: "..."
```

You can refer to the [Typesense](https://typesense.org/docs/27.1/api/vector-search.html#index-embeddings) documentation for the details of the configuration. 

Here is an example for a local ollama embedding that would generate embeddings from the "summary" and "tags" fields, with the `nomic-embed-text` model.
- Please note: `numDim` should be specified and corresponding to your model specifications
- You need to prefix your model name with `openai/` if you are not using Typesense's own embedding engine.

```
  embedding:
    name: embedding
    type: float[]
    index: true
    mapped: false
    numDim: 768
    embed:
      from: ["tags", "summary"]
      model_config:
        model_name: "openai/nomic-embed-text"
        api_key: '<key>'
        url: "http://localhost:11434/"
```


## Mapped attribute

Unless you want to store embeddings in your database, you can specify `mapped: false` in the configuration, the embeddings will only live in typesense.

In this case, we recommend excluding these fields from being retrieved in the query.
