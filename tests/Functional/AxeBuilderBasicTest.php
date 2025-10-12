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

namespace Playwright\Accessibility\Tests\Functional;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesTrait;
use Playwright\Accessibility\AssertsAccessibility;
use Playwright\Accessibility\AxeBuilder;
use Playwright\Accessibility\AxeResults;
use Playwright\Accessibility\Result\Node;
use Playwright\Accessibility\Result\Result;
use Playwright\Testing\PlaywrightTestCase;

#[CoversClass(AxeBuilder::class)]
#[CoversClass(AxeResults::class)]
#[UsesClass(Node::class)]
#[UsesClass(Result::class)]
#[UsesTrait(AssertsAccessibility::class)]
class AxeBuilderBasicTest extends PlaywrightTestCase
{
    use AssertsAccessibility;

    private function getFixtureUrl(string $filename): string
    {
        return 'file://'.__DIR__.'/../Fixtures/html/'.$filename;
    }

    public function testCanAnalyzeAccessiblePage(): void
    {
        $this->page->goto($this->getFixtureUrl('accessible.html'));

        $builder = new AxeBuilder($this->page);
        $results = $builder->analyze();

        $this->assertFalse($results->hasViolations());
        $this->assertSame(0, $results->getViolationCount());
        $this->assertIsAccessible($results);
    }

    public function testCanDetectColorContrastViolations(): void
    {
        $this->page->goto($this->getFixtureUrl('color-contrast-violations.html'));

        $builder = new AxeBuilder($this->page);
        $results = $builder->analyze();

        $this->assertTrue($results->hasViolations());
        $this->assertGreaterThan(0, $results->getViolationCount());

        // Find the color-contrast violation
        $violations = array_filter(
            $results->violations,
            fn ($v) => 'color-contrast' === $v->id
        );

        $this->assertNotEmpty($violations, 'Expected color-contrast violations to be found');

        $violation = array_values($violations)[0];
        $this->assertSame('color-contrast', $violation->id);
        $this->assertNotEmpty($violation->nodes);
    }

    public function testCanDetectMissingAltText(): void
    {
        $this->page->goto($this->getFixtureUrl('missing-alt-text.html'));

        $builder = new AxeBuilder($this->page);
        $results = $builder->analyze();

        $this->assertTrue($results->hasViolations());

        // Find image-alt violations
        $violations = array_filter(
            $results->violations,
            fn ($v) => 'image-alt' === $v->id
        );

        $this->assertNotEmpty($violations, 'Expected image-alt violations to be found');

        $violation = array_values($violations)[0];
        $this->assertSame('image-alt', $violation->id);
        $this->assertGreaterThan(0, count($violation->nodes));
    }

    public function testCanDetectMissingFormLabels(): void
    {
        $this->page->goto($this->getFixtureUrl('form-labels-missing.html'));

        $builder = new AxeBuilder($this->page);
        $results = $builder->analyze();

        $this->assertTrue($results->hasViolations());

        // Find label violations
        $violations = array_filter(
            $results->violations,
            fn ($v) => 'label' === $v->id
        );

        $this->assertNotEmpty($violations, 'Expected label violations to be found');
    }

    public function testCanDetectMultipleViolationTypes(): void
    {
        $this->page->goto($this->getFixtureUrl('multiple-violations.html'));

        $builder = new AxeBuilder($this->page);
        $results = $builder->analyze();

        $this->assertTrue($results->hasViolations());
        $this->assertGreaterThan(3, $results->getViolationCount(), 'Expected multiple violations');

        // Collect all violation IDs
        $violationIds = array_map(fn ($v) => $v->id, $results->violations);

        // Should have multiple different types of violations
        $this->assertGreaterThan(1, count(array_unique($violationIds)));
    }

    public function testResultsContainMetadata(): void
    {
        $this->page->goto($this->getFixtureUrl('accessible.html'));

        $builder = new AxeBuilder($this->page);
        $results = $builder->analyze();

        $this->assertNotNull($results->url);
        $this->assertStringContainsString('accessible.html', $results->url);
        $this->assertNotNull($results->timestamp);
    }

    public function testViolationsHaveRequiredFields(): void
    {
        $this->page->goto($this->getFixtureUrl('color-contrast-violations.html'));

        $builder = new AxeBuilder($this->page);
        $results = $builder->analyze();

        $this->assertNotEmpty($results->violations);

        $violation = $results->violations[0];
        $this->assertNotEmpty($violation->id);
        $this->assertNotEmpty($violation->help);
        $this->assertNotEmpty($violation->helpUrl);
        $this->assertNotEmpty($violation->tags);
        $this->assertIsArray($violation->nodes);
    }

    public function testNodesContainTargetSelectors(): void
    {
        $this->page->goto($this->getFixtureUrl('missing-alt-text.html'));

        $builder = new AxeBuilder($this->page);
        $results = $builder->analyze();

        $this->assertNotEmpty($results->violations);

        $violation = $results->violations[0];
        $this->assertNotEmpty($violation->nodes);

        $node = $violation->nodes[0];
        $this->assertNotEmpty($node->target);
        $this->assertIsArray($node->target);
        $this->assertNotEmpty($node->getSelector());
    }
}
