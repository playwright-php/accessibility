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

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\CoversTrait;
use Playwright\Accessibility\AssertsAccessibility;
use Playwright\Accessibility\AxeBuilder;
use Playwright\Testing\PlaywrightTestCase;

#[CoversTrait(AssertsAccessibility::class)]
class AssertsAccessibilityIntegrationTest extends PlaywrightTestCase
{
    use AssertsAccessibility;

    private function getFixtureUrl(string $filename): string
    {
        return 'file://'.__DIR__.'/../Fixtures/html/'.$filename;
    }

    public function testAssertIsAccessiblePassesForCleanPage(): void
    {
        $this->expectNotToPerformAssertions();

        $this->page->goto($this->getFixtureUrl('accessible.html'));

        $builder = new AxeBuilder($this->page);

        // Should not throw - test passes if this doesn't throw an exception
        $this->assertIsAccessible($builder);
    }

    public function testAssertIsAccessibleFailsForViolations(): void
    {
        $this->page->goto($this->getFixtureUrl('color-contrast-violations.html'));

        $builder = new AxeBuilder($this->page);

        try {
            $this->assertIsAccessible($builder);
            $this->fail('Expected AssertionFailedError to be thrown');
        } catch (AssertionFailedError $e) {
            $message = $e->getMessage();

            // Verify the error message contains useful information
            $this->assertStringContainsString('Failed asserting that the page is accessible', $message);
            $this->assertStringContainsString('violation', $message);
            $this->assertStringContainsString('color-contrast', $message);
        }
    }

    public function testAssertIsAccessibleWorksWithPage(): void
    {
        $this->expectNotToPerformAssertions();

        $this->page->goto($this->getFixtureUrl('accessible.html'));

        // Pass Page object directly - should not throw
        $this->assertIsAccessible($this->page);
    }

    public function testAssertIsAccessibleWorksWithBuilder(): void
    {
        $this->expectNotToPerformAssertions();

        $this->page->goto($this->getFixtureUrl('accessible.html'));

        $builder = new AxeBuilder($this->page);

        // Pass configured builder - should not throw
        $this->assertIsAccessible($builder);
    }

    public function testAssertIsAccessibleWorksWithResults(): void
    {
        $this->expectNotToPerformAssertions();

        $this->page->goto($this->getFixtureUrl('accessible.html'));

        $builder = new AxeBuilder($this->page);
        $results = $builder->analyze();

        // Pass AxeResults directly - should not throw
        $this->assertIsAccessible($results);
    }

    public function testFailureMessageIncludesMultipleViolations(): void
    {
        $this->page->goto($this->getFixtureUrl('multiple-violations.html'));

        try {
            $this->assertIsAccessible($this->page);
            $this->fail('Expected AssertionFailedError to be thrown');
        } catch (AssertionFailedError $e) {
            $message = $e->getMessage();

            // Should show multiple violations
            $this->assertStringContainsString('violation', $message);

            // Should include help URLs
            $this->assertStringContainsString('dequeuniversity.com', $message);

            // Should show impacted nodes
            $this->assertStringContainsString('Impacted nodes:', $message);
        }
    }

    public function testFailureMessageIncludesCustomMessage(): void
    {
        $this->page->goto($this->getFixtureUrl('color-contrast-violations.html'));

        $customMessage = 'Homepage accessibility check failed';

        try {
            $this->assertIsAccessible($this->page, $customMessage);
            $this->fail('Expected AssertionFailedError to be thrown');
        } catch (AssertionFailedError $e) {
            $message = $e->getMessage();

            $this->assertStringContainsString($customMessage, $message);
        }
    }

    public function testWorksWithConfiguredBuilder(): void
    {
        $this->expectNotToPerformAssertions();

        $this->page->goto($this->getFixtureUrl('multiple-violations.html'));

        // Disable color-contrast to make page "pass"
        $builder = (new AxeBuilder($this->page))
            ->withoutRules(['color-contrast', 'image-alt', 'label', 'button-name', 'link-name', 'heading-order']);

        // Should pass with these rules disabled
        $this->assertIsAccessible($builder);
    }
}
