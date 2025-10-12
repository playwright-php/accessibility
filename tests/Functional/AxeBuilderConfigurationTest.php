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
use Playwright\Accessibility\AxeBuilder;
use Playwright\Accessibility\AxeResults;
use Playwright\Accessibility\Result\Node;
use Playwright\Accessibility\Result\Result;
use Playwright\Accessibility\RuleId;
use Playwright\Accessibility\WcagTag;
use Playwright\Testing\PlaywrightTestCase;

#[CoversClass(AxeBuilder::class)]
#[UsesClass(AxeResults::class)]
#[UsesClass(Node::class)]
#[UsesClass(Result::class)]
class AxeBuilderConfigurationTest extends PlaywrightTestCase
{
    private function getFixtureUrl(string $filename): string
    {
        return 'file://'.__DIR__.'/../Fixtures/html/'.$filename;
    }

    public function testWithinScopesAnalysisToElement(): void
    {
        $this->page->goto($this->getFixtureUrl('multiple-violations.html'));

        // First, analyze the whole page
        $fullResults = (new AxeBuilder($this->page))->analyze();
        $fullCount = $fullResults->getViolationCount();

        // Now analyze only a specific section (that we know has fewer issues)
        $scopedResults = (new AxeBuilder($this->page))
            ->within('section:last-of-type')
            ->analyze();

        // Scoped analysis should find fewer violations
        $this->assertLessThanOrEqual($fullCount, $scopedResults->getViolationCount());
    }

    public function testExcludeIgnoresElements(): void
    {
        $this->page->goto($this->getFixtureUrl('multiple-violations.html'));

        // Analyze without exclusions
        $fullResults = (new AxeBuilder($this->page))->analyze();

        // Analyze excluding the section with heading order violation
        // This excludes the last section which contains the h4 that skips h3
        $excludedResults = (new AxeBuilder($this->page))
            ->exclude('section:last-of-type')
            ->analyze();

        // Results should be valid (exclude works without errors)
        $this->assertInstanceOf(AxeResults::class, $excludedResults);

        // When we exclude elements with violations, we should have fewer or equal violations
        $this->assertLessThanOrEqual(
            $fullResults->getViolationCount(),
            $excludedResults->getViolationCount()
        );
    }

    public function testWithTagsRunsOnlySpecificStandards(): void
    {
        $this->page->goto($this->getFixtureUrl('missing-alt-text.html'));

        // Run with specific WCAG tags using enum
        // Note: WCAG_2_0_A covers WCAG 2.0 Level A rules (which includes image-alt)
        $results = (new AxeBuilder($this->page))
            ->withTags([WcagTag::WCAG_2_0_A])
            ->analyze();

        // Should have image-alt violations (WCAG 2.0 Level A rule)
        $this->assertGreaterThan(0, $results->getViolationCount());

        // All violations should be tagged with wcag2a
        foreach ($results->violations as $violation) {
            $this->assertContains(
                WcagTag::WCAG_2_0_A->value,
                $violation->tags,
                "Violation {$violation->id} should be tagged with wcag2a"
            );
        }
    }

    public function testWithoutRulesDisablesSpecificChecks(): void
    {
        $this->page->goto($this->getFixtureUrl('color-contrast-violations.html'));

        // First check: should find color-contrast violations
        $withContrastResults = (new AxeBuilder($this->page))->analyze();

        $hasColorContrast = false;
        foreach ($withContrastResults->violations as $violation) {
            if (RuleId::COLOR_CONTRAST->value === $violation->id) {
                $hasColorContrast = true;
                break;
            }
        }

        $this->assertTrue($hasColorContrast, 'Should find color-contrast violations initially');

        // Now disable color-contrast rule using enum
        $withoutContrastResults = (new AxeBuilder($this->page))
            ->withoutRules([RuleId::COLOR_CONTRAST])
            ->analyze();

        // Should not have color-contrast violations
        foreach ($withoutContrastResults->violations as $violation) {
            $this->assertNotSame(RuleId::COLOR_CONTRAST->value, $violation->id);
        }
    }

    public function testCanChainMultipleConfigOptions(): void
    {
        $this->page->goto($this->getFixtureUrl('multiple-violations.html'));

        // Chain multiple configuration options using enums
        $results = (new AxeBuilder($this->page))
            ->within('main')
            ->exclude('section:first-of-type')
            ->withTags([WcagTag::WCAG_2_0_A, WcagTag::WCAG_2_0_AA])
            ->withoutRules([RuleId::COLOR_CONTRAST])
            ->analyze();

        // Verify color-contrast was disabled
        foreach ($results->violations as $violation) {
            $this->assertNotSame(RuleId::COLOR_CONTRAST->value, $violation->id);
        }

        // This should not throw - just verify chaining works
        $this->assertInstanceOf(AxeResults::class, $results);
    }

    public function testBuilderReturnsSelfForChaining(): void
    {
        $builder = new AxeBuilder($this->page);

        $this->assertSame($builder, $builder->within('main'));
        $this->assertSame($builder, $builder->exclude('#foo'));
        $this->assertSame($builder, $builder->withTags([WcagTag::WCAG_2_0_AA]));
        $this->assertSame($builder, $builder->withoutRules([RuleId::COLOR_CONTRAST]));
    }
}
