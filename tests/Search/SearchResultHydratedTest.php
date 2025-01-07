<?php

namespace Biblioteca\TypesenseBundle\Tests\Search;

use Biblioteca\TypesenseBundle\Search\Results\SearchResults;
use Biblioteca\TypesenseBundle\Search\Results\SearchResultsHydrated;

class SearchResultHydratedTest extends \PHPUnit\Framework\TestCase
{
    public const DATA = [
        'facet_counts' => [],
        'found' => 120,
        'search_time_ms' => 15,
        'page' => 2,
        'out_of' => 120,
        'hits' => [
            [
                'document' => [
                    'id' => '1',
                    'title' => 'Introduction to Typesense',
                    'content' => 'Typesense is a modern, fast, typo-tolerant search engine...',
                    'author' => 'John Doe',
                    'tags' => ['search', 'API', 'typesense'],
                ],
                'highlights' => [
                    [
                        'field' => 'content',
                        'snippet' => 'Typesense is a modern, fast, typo-tolerant <em>search</em> engine...',
                        'matched_tokens' => ['search'],
                    ],
                ],
            ],
            [
                'document' => [
                    'id' => '2',
                    'title' => 'Advanced Typesense Features',
                    'content' => 'Learn about advanced features like faceting, filtering, and more.',
                    'author' => 'Jane Smith',
                    'tags' => ['advanced', 'features', 'typesense'],
                ],
                'highlights' => [
                    [
                        'field' => 'title',
                        'snippet' => 'Advanced <em>Typesense</em> Features',
                        'matched_tokens' => ['typesense'],
                    ],
                ],
            ],
        ],
        'per_page' => 10,
        'total_pages' => 12,
    ];

    public function testFound(): void
    {
        $searchResultsHydrated = $this->getResult();
        $this->assertEquals(120, $searchResultsHydrated->found());
    }

    public function testFacetCounts(): void
    {
        $searchResultsHydrated = $this->getResult();
        $this->assertEquals([], $searchResultsHydrated->getFacetCounts());
    }

    public function testTotalPage(): void
    {
        $searchResultsHydrated = $this->getResult();
        $this->assertEquals(12, $searchResultsHydrated->getTotalPage());
    }

    public function testHits(): void
    {
        $searchResultsHydrated = $this->getResult();
        $this->assertCount(2, $searchResultsHydrated->getIterator());
    }

    public function testIterator(): void
    {
        $searchResultsHydrated = $this->getResult();
        $objects = iterator_to_array($searchResultsHydrated->getIterator());
        $this->assertCount(2, $objects);
        $this->assertSame(['_1', '_2'], array_keys($objects), 'Keys should be preserved');
    }

    public function testToArray(): void
    {
        $searchResultsHydrated = $this->getResult();
        $this->assertEquals(self::DATA, $searchResultsHydrated->toArray());
    }

    /**
     * @return SearchResultsHydrated<object>
     */
    private function getResult(): SearchResultsHydrated
    {
        $objects = $this->getObjects();

        return SearchResultsHydrated::fromPayloadAndCollection(self::DATA, $objects);
    }

    /**
     * @return array<string, object>
     */
    private function getObjects(): array
    {
        $objects = [];
        foreach (self::DATA['hits'] as $hit) {
            $document = (object) $hit['document'];
            $objects['_'.$document->id] = $document;
        }

        return $objects;
    }

    public function testResultConstructor(): void
    {
        $objects = $this->getObjects();
        $searchResults = new SearchResults(self::DATA);

        $searchResultsHydrated = SearchResultsHydrated::fromResultAndCollection($searchResults, $objects);
        $this->assertSame(self::DATA, $searchResultsHydrated->toArray());
        $this->assertCount(2, $searchResultsHydrated->getIterator());
    }

    public function testPayloadConstructor(): void
    {
        $searchResultsHydrated = SearchResultsHydrated::fromPayload(self::DATA);
        $this->assertSame(self::DATA, $searchResultsHydrated->toArray());
        $this->assertCount(0, $searchResultsHydrated->getIterator());
    }
}
