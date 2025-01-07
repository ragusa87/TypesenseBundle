<?php

namespace Biblioteca\TypesenseBundle\Tests\Search;

use Biblioteca\TypesenseBundle\Search\Results\SearchResults;

class SearchResultTest extends \PHPUnit\Framework\TestCase
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
        $searchResults = $this->getResult();
        $this->assertEquals(120, $searchResults->found());
    }

    public function testFacetCounts(): void
    {
        $searchResults = $this->getResult();
        $this->assertEquals([], $searchResults->getFacetCounts());
    }

    public function testTotalPage(): void
    {
        $searchResults = $this->getResult();
        $this->assertEquals(12, $searchResults->getTotalPage());
    }

    public function testHits(): void
    {
        $searchResults = $this->getResult();
        $this->assertCount(2, $searchResults->getIterator());
    }

    public function testIterator(): void
    {
        $searchResults = $this->getResult();
        $data = iterator_to_array($searchResults->getIterator());
        $this->assertCount(2, $data);
        $this->assertSame([0, 1], array_keys($data), 'Keys should be preserved');
    }

    public function testToArray(): void
    {
        $searchResults = $this->getResult();
        $this->assertEquals(self::DATA, $searchResults->toArray());
    }

    public function testEmptyIterator(): void
    {
        $data = self::DATA;
        $data['hits'] = [[], []]; // invalid hits

        $searchResults = new SearchResults($data);
        $this->assertCount(0, $searchResults->getIterator());
    }

    private function getResult(): SearchResults
    {
        return new SearchResults(self::DATA);
    }
}
