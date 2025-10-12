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

use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\TestCase;
use Playwright\Accessibility\AssertsAccessibility;
use Playwright\Accessibility\AxeResults;
use Playwright\Accessibility\Result\Node;
use Playwright\Accessibility\Result\Violation;

#[CoversClass(AxeResults::class)]
#[CoversClass(Violation::class)]
#[CoversClass(Node::class)]
#[CoversTrait(AssertsAccessibility::class)]
final class AssertsAccessibilityTest extends TestCase
{
    use AssertsAccessibility;

    public function testAssertIsAccessiblePassesWhenNoViolations(): void
    {
        $this->expectNotToPerformAssertions();

        $results = new AxeResults();

        // Should not throw when no violations exist
        $this->assertIsAccessible($results);
    }

    public function testAssertIsAccessibleFailsWhenViolationsExist(): void
    {
        $violation = new Violation(
            id: 'color-contrast',
            description: 'Ensures the contrast between foreground and background colors',
            help: 'Elements must have sufficient color contrast',
            helpUrl: 'https://dequeuniversity.com/rules/axe/4.4/color-contrast',
            nodes: [
                new Node(target: ['.text-muted']),
                new Node(target: ['footer a']),
            ],
            tags: ['wcag2aa'],
            impact: 'serious',
        );

        $results = new AxeResults(violations: [$violation]);

        try {
            $this->assertIsAccessible($results);
            $this->fail('Expected AssertionFailedError to be thrown');
        } catch (AssertionFailedError $e) {
            $message = $e->getMessage();

            // Check that the message contains key information
            $this->assertStringContainsString('Failed asserting that the page is accessible', $message);
            $this->assertStringContainsString('Found 1 violation', $message);
            $this->assertStringContainsString('color-contrast', $message);
            $this->assertStringContainsString('serious', $message);
            $this->assertStringContainsString('.text-muted', $message);
            $this->assertStringContainsString('footer a', $message);
            $this->assertStringContainsString('https://dequeuniversity.com/rules/axe/4.4/color-contrast', $message);
        }
    }

    public function testAssertIsAccessibleFormatsMultipleViolations(): void
    {
        $violation1 = new Violation(
            id: 'color-contrast',
            description: 'Color contrast',
            help: 'Elements must have sufficient color contrast',
            helpUrl: 'https://example.com/color-contrast',
            nodes: [new Node(target: ['.text-muted'])],
            tags: ['wcag2aa'],
            impact: 'serious',
        );

        $violation2 = new Violation(
            id: 'image-alt',
            description: 'Image alt',
            help: 'Images must have alternate text',
            helpUrl: 'https://example.com/image-alt',
            nodes: [new Node(target: ['img.logo'])],
            tags: ['wcag2a'],
            impact: 'critical',
        );

        $results = new AxeResults(violations: [$violation1, $violation2]);

        try {
            $this->assertIsAccessible($results);
            $this->fail('Expected AssertionFailedError to be thrown');
        } catch (AssertionFailedError $e) {
            $message = $e->getMessage();

            $this->assertStringContainsString('Found 2 violations', $message);
            $this->assertStringContainsString('1) color-contrast', $message);
            $this->assertStringContainsString('2) image-alt', $message);
        }
    }

    public function testAssertIsAccessibleIncludesCustomMessage(): void
    {
        $violation = new Violation(
            id: 'test',
            description: 'Test',
            help: 'Test help',
            helpUrl: 'https://example.com',
            nodes: [],
            tags: [],
        );

        $results = new AxeResults(violations: [$violation]);

        try {
            $this->assertIsAccessible($results, 'Custom failure message');
            $this->fail('Expected AssertionFailedError to be thrown');
        } catch (AssertionFailedError $e) {
            $message = $e->getMessage();

            $this->assertStringContainsString('Custom failure message', $message);
        }
    }
}
