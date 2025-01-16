<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper\Entity;

use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueConversionException;
use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueExtractorException;
use Biblioteca\TypesenseBundle\Mapper\Converter\ValueConverterInterface;
use Biblioteca\TypesenseBundle\Mapper\Entity\EntityTransformer;
use Biblioteca\TypesenseBundle\Mapper\Fields\FieldMapping;
use Biblioteca\TypesenseBundle\Mapper\Mapping\Mapping;
use Biblioteca\TypesenseBundle\Mapper\Mapping\MappingInterface;
use Biblioteca\TypesenseBundle\Mapper\MappingGeneratorInterface;
use Biblioteca\TypesenseBundle\Mapper\Options\CollectionOptions;
use Biblioteca\TypesenseBundle\Type\DataTypeEnum;
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
