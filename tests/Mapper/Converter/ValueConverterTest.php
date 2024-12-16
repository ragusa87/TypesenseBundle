<?php

namespace Biblioteca\TypesenseBundle\Tests\Mapper\Converter;

use Biblioteca\TypesenseBundle\Mapper\Converter\Exception\ValueConversionException;
use Biblioteca\TypesenseBundle\Mapper\Converter\ToTypesenseInterface;
use Biblioteca\TypesenseBundle\Mapper\Converter\ValueConverter;
use Biblioteca\TypesenseBundle\Type\DataTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ValueConverter::class)]
class ValueConverterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws ValueConversionException
     */
    public function testSimple(): void
    {
        $valueConverter = new ValueConverter();

        $this->assertSame('12', $valueConverter->convert(12, DataTypeEnum::STRING->value));
        $this->assertSame(['12', '13'], $valueConverter->convert([12, 13], DataTypeEnum::STRING_ARRAY->value));
        $this->assertSame([12, 13], $valueConverter->convert([12, 13], DataTypeEnum::INT32_ARRAY->value));
        $this->assertSame([12.5, 13.3], $valueConverter->convert([12.5, 13.3], DataTypeEnum::FLOAT_ARRAY->value));
        $this->assertSame(true, $valueConverter->convert(1, DataTypeEnum::BOOL->value));
        $this->assertSame([true, false, true, false], $valueConverter->convert([1, 0, 'true', '0'], DataTypeEnum::BOOL_ARRAY->value));
        $this->assertSame([51.4874004, -0.016055], $valueConverter->convert([51.4874004, -0.016055], DataTypeEnum::GEOPOINT->value));
        $this->assertSame([[51.4874004, -0.016055], null], $valueConverter->convert([[51.4874004, -0.016055], null], DataTypeEnum::GEOPOINT_ARRAY->value));

        foreach (DataTypeEnum::cases() as $type) {
            $this->assertSame(null, $valueConverter->convert(null, $type->value));
        }

        foreach ([
            DataTypeEnum::STRING->value => '',
            DataTypeEnum::INT32->value => 0,
            DataTypeEnum::BOOL->value => false,
            DataTypeEnum::OBJECT->value => [],
        ] as $type => $expectedValue) {
            $this->assertSame($expectedValue, $valueConverter->convert(null, $type, false));
        }
    }

    /**
     * @throws ValueConversionException
     */
    public function testDate(): void
    {
        $valueConverter = new ValueConverter();

        $date = new \DateTimeImmutable('now');
        $timeStamp = $date->getTimestamp();
        $this->assertSame($timeStamp, $valueConverter->convert($date, DataTypeEnum::INT64->value));
    }

    public function testImage(): void
    {
        $valueConverter = new ValueConverter();

        $expected = base64_encode('test');
        $this->assertSame($expected, $valueConverter->convert($expected, DataTypeEnum::IMAGE->value));
    }

    public function testImageFile(): void
    {
        $valueConverter = new ValueConverter();
        $file = new \SplFileInfo(__DIR__.'/test.txt');
        $this->assertSame('QmFzZTY0IEVuY29kZSBtZS4=', $valueConverter->convert($file, DataTypeEnum::IMAGE->value));
    }

    /**
     * @throws ValueConversionException
     */
    public function testObjects(): void
    {
        $valueConverter = new ValueConverter();

        $object = new class implements \JsonSerializable {
            /**
             * @return string[]
             */
            public function jsonSerialize(): array
            {
                return ['key' => 'value'];
            }
        };
        $this->assertSame(['key' => 'value'], $valueConverter->convert($object, DataTypeEnum::OBJECT->value));
        $this->assertSame(['key' => 'value'], $valueConverter->convert(['key' => 'value'], DataTypeEnum::OBJECT->value));

        $object = new class implements ToTypesenseInterface {
            public function toTypesense(): array
            {
                return ['key' => 'value'];
            }
        };
        $this->assertSame(['key' => 'value'], $valueConverter->convert($object, DataTypeEnum::OBJECT->value));

        $object = new \ArrayIterator(['key' => 'value']);
        $this->assertSame(['key' => 'value'], $valueConverter->convert($object, DataTypeEnum::OBJECT->value));

        try {
            $object = new \stdClass();
            $this->assertSame(['key' => 'value'], $valueConverter->convert($object, DataTypeEnum::OBJECT->value));
            $failed = true;
        } catch (ValueConversionException) {
            $failed = false;
        }
        $this->assertFalse($failed, 'Exception ValueConversionException not thrown');
    }
}
