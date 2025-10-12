<?php

declare(strict_types=1);

/*
 * This file is part of the community-maintained Playwright PHP project.
 * It is not affiliated with or endorsed by Microsoft.
 *
 * (c) 2025-Present - Playwright PHP - https://github.com/playwright-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Playwright\Accessibility\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Playwright\Accessibility\AxeResults;
use Playwright\Accessibility\Result\Inapplicable;
use Playwright\Accessibility\Result\Incomplete;
use Playwright\Accessibility\Result\Node;
use Playwright\Accessibility\Result\Pass;
use Playwright\Accessibility\Result\Result;
use Playwright\Accessibility\Result\Violation;

#[CoversClass(AxeResults::class)]
#[CoversClass(Violation::class)]
#[CoversClass(Pass::class)]
#[CoversClass(Incomplete::class)]
#[CoversClass(Inapplicable::class)]
#[CoversClass(Result::class)]
#[CoversClass(Node::class)]
final class AxeResultsTest extends TestCase
{
    public function testCanCreateResultsFromArray(): void
    {
        $data = [
            'violations' => [
                [
                    'id' => 'color-contrast',
                    'description' => 'Color contrast',
                    'help' => 'Elements must have sufficient color contrast',
                    'helpUrl' => 'https://example.com',
                    'impact' => 'serious',
                    'tags' => ['wcag2aa'],
                    'nodes' => [['target' => ['.text-muted']]],
                ],
            ],
            'passes' => [
                [
                    'id' => 'document-title',
                    'description' => 'Document title',
                    'help' => 'Documents must have a title',
                    'helpUrl' => 'https://example.com',
                    'tags' => ['wcag2a'],
                    'nodes' => [],
                ],
            ],
            'incomplete' => [],
            'inapplicable' => [],
            'url' => 'https://example.com',
            'timestamp' => '2025-01-01T00:00:00.000Z',
        ];

        $results = AxeResults::fromArray($data);

        $this->assertCount(1, $results->violations);
        $this->assertInstanceOf(Violation::class, $results->violations[0]);
        $this->assertSame('color-contrast', $results->violations[0]->id);

        $this->assertCount(1, $results->passes);
        $this->assertInstanceOf(Pass::class, $results->passes[0]);
        $this->assertSame('document-title', $results->passes[0]->id);

        $this->assertCount(0, $results->incomplete);
        $this->assertCount(0, $results->inapplicable);

        $this->assertSame('https://example.com', $results->url);
        $this->assertSame('2025-01-01T00:00:00.000Z', $results->timestamp);
    }

    public function testHasViolationsReturnsTrueWhenViolationsExist(): void
    {
        $results = new AxeResults(
            violations: [
                new Violation('test', 'desc', 'help', 'url', [], ['tag']),
            ]
        );

        $this->assertTrue($results->hasViolations());
    }

    public function testHasViolationsReturnsFalseWhenNoViolations(): void
    {
        $results = new AxeResults();

        $this->assertFalse($results->hasViolations());
    }

    public function testGetViolationCountReturnsCorrectCount(): void
    {
        $results = new AxeResults(
            violations: [
                new Violation('test1', 'desc', 'help', 'url', [], ['tag']),
                new Violation('test2', 'desc', 'help', 'url', [], ['tag']),
                new Violation('test3', 'desc', 'help', 'url', [], ['tag']),
            ]
        );

        $this->assertSame(3, $results->getViolationCount());
    }

    public function testCanCreateEmptyResults(): void
    {
        $results = new AxeResults();

        $this->assertCount(0, $results->violations);
        $this->assertCount(0, $results->passes);
        $this->assertCount(0, $results->incomplete);
        $this->assertCount(0, $results->inapplicable);
        $this->assertNull($results->url);
        $this->assertNull($results->timestamp);
        $this->assertFalse($results->hasViolations());
        $this->assertSame(0, $results->getViolationCount());
    }

    public function testAllResultTypesAreCreatedCorrectly(): void
    {
        $data = [
            'violations' => [['id' => 'v1', 'description' => '', 'help' => '', 'helpUrl' => '', 'tags' => [], 'nodes' => []]],
            'passes' => [['id' => 'p1', 'description' => '', 'help' => '', 'helpUrl' => '', 'tags' => [], 'nodes' => []]],
            'incomplete' => [['id' => 'i1', 'description' => '', 'help' => '', 'helpUrl' => '', 'tags' => [], 'nodes' => []]],
            'inapplicable' => [['id' => 'ia1', 'description' => '', 'help' => '', 'helpUrl' => '', 'tags' => [], 'nodes' => []]],
        ];

        $results = AxeResults::fromArray($data);

        $this->assertInstanceOf(Violation::class, $results->violations[0]);
        $this->assertInstanceOf(Pass::class, $results->passes[0]);
        $this->assertInstanceOf(Incomplete::class, $results->incomplete[0]);
        $this->assertInstanceOf(Inapplicable::class, $results->inapplicable[0]);
    }
}
