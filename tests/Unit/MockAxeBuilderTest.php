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
use PHPUnit\Framework\Attributes\UsesTrait;
use PHPUnit\Framework\TestCase;
use Playwright\Accessibility\AssertsAccessibility;
use Playwright\Accessibility\AxeResults;
use Playwright\Accessibility\Result\Node;
use Playwright\Accessibility\Result\Violation;
use Playwright\Accessibility\Testing\MockAxeBuilder;

#[CoversClass(MockAxeBuilder::class)]
#[CoversClass(AxeResults::class)]
#[CoversClass(Violation::class)]
#[CoversClass(Node::class)]
#[UsesTrait(AssertsAccessibility::class)]
final class MockAxeBuilderTest extends TestCase
{
    use AssertsAccessibility;

    public function testCanCreateMockWithNoViolations(): void
    {
        $mock = MockAxeBuilder::create()
            ->withNoViolations();

        $results = $mock->analyze();

        $this->assertFalse($results->hasViolations());
        $this->assertCount(0, $results->violations);
        $this->assertGreaterThan(0, count($results->passes));
    }

    public function testCanCreateMockWithViolations(): void
    {
        $mock = MockAxeBuilder::create()
            ->withViolation('color-contrast', 'serious', ['.text-muted', 'footer a'])
            ->withViolation('image-alt', 'critical', ['img.logo']);

        $results = $mock->analyze();

        $this->assertTrue($results->hasViolations());
        $this->assertCount(2, $results->violations);

        $this->assertSame('color-contrast', $results->violations[0]->id);
        $this->assertSame('serious', $results->violations[0]->impact);
        $this->assertCount(2, $results->violations[0]->nodes);

        $this->assertSame('image-alt', $results->violations[1]->id);
        $this->assertSame('critical', $results->violations[1]->impact);
        $this->assertCount(1, $results->violations[1]->nodes);
    }

    public function testWithViolationGeneratesHelpText(): void
    {
        $mock = MockAxeBuilder::create()
            ->withViolation('color-contrast');

        $results = $mock->analyze();

        $this->assertSame('Elements must have sufficient color contrast', $results->violations[0]->help);
        $this->assertStringContainsString('dequeuniversity.com', $results->violations[0]->helpUrl);
    }

    public function testCanAddPasses(): void
    {
        $mock = MockAxeBuilder::create()
            ->withPass('document-title')
            ->withPass('html-has-lang');

        $results = $mock->analyze();

        $this->assertCount(2, $results->passes);
        $this->assertSame('document-title', $results->passes[0]->id);
        $this->assertSame('html-has-lang', $results->passes[1]->id);
    }

    public function testCanAddIncompleteAndInapplicable(): void
    {
        $mock = MockAxeBuilder::create()
            ->withIncomplete('rule1')
            ->withInapplicable('rule2');

        $results = $mock->analyze();

        $this->assertCount(1, $results->incomplete);
        $this->assertCount(1, $results->inapplicable);
    }

    public function testWithoutRulesFiltersViolations(): void
    {
        $mock = MockAxeBuilder::create()
            ->withViolation('color-contrast')
            ->withViolation('image-alt')
            ->withoutRules(['color-contrast']);

        $results = $mock->analyze();

        $this->assertCount(1, $results->violations);
        $this->assertSame('image-alt', $results->violations[0]->id);
    }

    public function testFluentApiMethodsAreCompatible(): void
    {
        // These should not throw and should return self
        $mock = MockAxeBuilder::create()
            ->within('#main')
            ->exclude('#chat')
            ->withTags(['wcag2aa'])
            ->withViolation('test');

        $this->assertInstanceOf(MockAxeBuilder::class, $mock);
    }

    public function testMockCanBeUsedWithAssertsAccessibility(): void
    {
        $mock = MockAxeBuilder::create()
            ->withNoViolations();

        // Should pass
        $this->assertIsAccessible($mock->analyze());

        // Should fail
        $mockWithViolations = MockAxeBuilder::create()
            ->withViolation('color-contrast');

        $this->expectException(\PHPUnit\Framework\AssertionFailedError::class);
        $this->expectExceptionMessage('Failed asserting that the page is accessible');

        $this->assertIsAccessible($mockWithViolations->analyze());
    }

    public function testResultsHaveReasonableMetadata(): void
    {
        $mock = MockAxeBuilder::create()
            ->withViolation('color-contrast');

        $results = $mock->analyze();

        $this->assertSame('http://mock-test.local', $results->url);
        $this->assertNotNull($results->timestamp);
    }
}
