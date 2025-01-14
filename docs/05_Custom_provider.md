You can also populate additional collection if you inject a service implementing `StandaloneMapperInterface`.
You will need to convert the data on the fly, and also declare the mapping configuration yourself.

You will not be able to use the `hydrated` result out of the box.

Example of a custom mapper, it uses `AbstractEntityMapper` that will fetch the entities from the database, but it's not mandatory.

```php
<?php

namespace App\Mapper;

use App\Entity\Book;
use Biblioteca\TypesenseBundle\Mapper\Entity\AbstractEntityDataGenerator;
use Biblioteca\TypesenseBundle\Mapper\Mapping\Mapping;
use Biblioteca\TypesenseBundle\Mapper\MappingGeneratorInterface;
use Biblioteca\TypesenseBundle\Mapper\StandaloneCollectionManagerInterface;
use Biblioteca\TypesenseBundle\Type\DataTypeEnum;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends AbstractEntityDataGenerator<Book>
 */
class BookDataGenerator extends AbstractEntityDataGenerator
{
    public function __construct(
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager, Book::class);
    }

    public static function getName(): string
    {
        return 'book';
    }
    
    public function getMapping(): Mapping
    {
        $mapping = new Mapping(collectionOptions: new CollectionOptions(
            tokenSeparators: [' ', '-', "'"],
            symbolsToIndex: ['+', '#', '@', '_'],
            defaultSortingField: 'sortable_id'
        ));

        return $mapping->
            add(
                name: 'id',
                type: DataTypeEnum::STRING
            )
            ->add(
                name: 'title',
                type: DataTypeEnum::STRING
            )
            ->add(
                name: 'sortable_id',
                type: DataTypeEnum::INT32
            )
            ->add(
                name: 'serie',
                type: DataTypeEnum::STRING,
                facet: true,
                optional: true
            )
            ->add(
                name: 'summary',
                type: DataTypeEnum::STRING,
                optional: true
            )
            ->add(
                name: 'serieIndex',
                type: DataTypeEnum::STRING,
                optional: true
            )
            ->add(
                name: 'extension',
                type: DataTypeEnum::STRING,
                facet: true
            )
            ->add(
                name: 'authors',
                type: DataTypeEnum::STRING_ARRAY,
                facet: true
            )
            ->add(
                name: 'tags',
                type: DataTypeEnum::STRING_ARRAY,
                facet: true,
                optional: true
            )
        ;
    }

    public function transform(object $data): array
    {
        return [
            'id' => (string) $data->getId(),
            'title' => $data->getTitle(),
            'sortable_id' => $data->getId(),
            'serie' => (string) $data->getSerie(),
            'summary' => (string) $data->getSummary(),
            'serieIndex' => (string) $data->getSerieIndex(),
            'extension' => $data->getExtension(),
            'authors' => $data->getAuthors(),
            'tags' => $data->getTags(),
        ];
    }

    public function support(object $entity): bool
    {
        return $entity::class === Book::class;
    }
}
```
