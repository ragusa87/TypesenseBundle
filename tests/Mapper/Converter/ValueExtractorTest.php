<?php

namespace Biblioverse\TypesenseBundle\Tests\Mapper\Converter;

use Biblioverse\TypesenseBundle\Mapper\Converter\Exception\ValueExtractorException;
use Biblioverse\TypesenseBundle\Mapper\Converter\ValueExtractor;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ValueExtractor::class)]
class ValueExtractorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @throws ValueExtractorException
     */
    public function testComposed(): void
    {
        $valueExtractor = new ValueExtractor();
        $object = new class {
            /**
             * @return array{user: array{name: string}}
             */
            public function getConfig(): array
            {
                return ['user' => ['name' => 'hello world']];
            }
        };

        $this->assertSame('hello world', $valueExtractor->getValue($object, 'config.user.name'));
    }

    public function testSimple(): void
    {
        $valueExtractor = new ValueExtractor();
        $object = new class {
            public function getUniversalAnswer(): int
            {
                return 42;
            }
        };
        $this->assertSame(42, $valueExtractor->getValue($object, 'universal_answer'));
    }

    public function testNotReadable(): void
    {
        $valueExtractor = new ValueExtractor();
        $object = new class {
            /**
             * @return array{'number': int}
             */
            private function internal(): array // @phpstan-ignore method.unused (Called by the property accessor)
            {
                return ['number' => 42];
            }
        };
        $failed = true;
        try {
            $this->assertSame(42, $valueExtractor->getValue($object, 'internal.number'));
        } catch (ValueExtractorException) {
            $failed = false;
        }

        $this->assertFalse($failed, 'Should have failed to access a private property');
    }
}
