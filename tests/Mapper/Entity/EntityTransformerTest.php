<?php

namespace Biblioverse\TypesenseBundle\Tests\Mapper\Entity;

use Biblioverse\TypesenseBundle\Mapper\Converter\Exception\ValueConversionException;
use Biblioverse\TypesenseBundle\Mapper\Converter\Exception\ValueExtractorException;
use Biblioverse\TypesenseBundle\Mapper\Converter\ValueConverterInterface;
use Biblioverse\TypesenseBundle\Mapper\Entity\EntityTransformer;
use Biblioverse\TypesenseBundle\Mapper\Fields\FieldMapping;
use Biblioverse\TypesenseBundle\Mapper\Mapping\Mapping;
use Biblioverse\TypesenseBundle\Mapper\Mapping\MappingInterface;
use Biblioverse\TypesenseBundle\Mapper\MappingGeneratorInterface;
use Biblioverse\TypesenseBundle\Mapper\Options\CollectionOptions;
use Biblioverse\TypesenseBundle\Type\DataTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EntityTransformer::class)]
class EntityTransformerTest extends TestCase
{
    /**
     * @throws ValueExtractorException
     * @throws ValueConversionException
     */
    public function testTransform(): void
    {
        $entity = $this->getEntity();
        $entityTransformer = $this->getTransformer();
        $this->assertSame([
            'id' => '42',
            'title' => 'My Title',
            'composed' => 'my composition',
        ], $entityTransformer->transform($entity));
    }

    public function testSupport(): void
    {
        $entity = $this->getEntity();
        $entityTransformer = $this->getTransformer();
        $this->assertTrue($entityTransformer->support($entity));
    }

    public function getEntity(): object
    {
        return new class {
            public int $id = 42;
            public string $title = 'My Title';

            public function composed(): string
            {
                return 'my composition';
            }
        };
    }

    /**
     * @return EntityTransformer<object>
     */
    private function getTransformer(): EntityTransformer
    {
        $this->createMock(\Doctrine\ORM\EntityManagerInterface::class);
        $mappingGenerator = new class implements MappingGeneratorInterface {
            public function getMapping(): MappingInterface
            {
                $mapping = new Mapping(collectionOptions: new CollectionOptions(
                    tokenSeparators: [' ', '-', "'"],
                    symbolsToIndex: ['+', '#', '@', '_'],
                    defaultSortingField: 'sortable_id'
                ));

                $mapping->
                add(
                    name: 'id',
                    type: DataTypeEnum::STRING
                )
                        ->add(
                            name: 'title',
                            type: DataTypeEnum::STRING
                        );

                $mapping->addField(new FieldMapping(name: 'composed', type: 'string'));

                return $mapping;
            }
        };

        $valueConverter = new class implements ValueConverterInterface {
            public function convert(mixed $value, string $type, bool $optional = true): mixed
            {
                return is_scalar($value) ? (string) $value : '[not-scalar]';
            }
        };

        return new EntityTransformer($mappingGenerator, $valueConverter);
    }
}
