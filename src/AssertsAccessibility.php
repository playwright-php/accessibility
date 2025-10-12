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

namespace Playwright\Accessibility;

use PHPUnit\Framework\Assert;
use Playwright\Accessibility\Result\Violation;
use Playwright\Page\PageInterface;

/**
 * Trait for PHPUnit tests to provide accessibility assertions.
 */
trait AssertsAccessibility
{
    /**
     * Assert that a page or builder has no accessibility violations.
     *
     * @param AxeBuilder|AxeResults|PageInterface $subject The builder, results, or page to check
     * @param string                              $message Optional custom failure message
     */
    public function assertIsAccessible(AxeBuilder|AxeResults|PageInterface $subject, string $message = ''): void
    {
        // Convert to AxeResults
        if ($subject instanceof PageInterface) {
            $results = (new AxeBuilder($subject))->analyze();
        } elseif ($subject instanceof AxeBuilder) {
            $results = $subject->analyze();
        } else {
            $results = $subject;
        }

        // If no violations, test passes (no assertion needed - absence of failure is success)
        if (!$results->hasViolations()) {
            return;
        }

        // Build a beautiful failure message
        $failureMessage = $this->formatViolations($results->violations);

        if ('' !== $message) {
            $failureMessage = $message."\n\n".$failureMessage;
        }

        Assert::fail($failureMessage);
    }

    /**
     * Format violations into a human-readable message.
     *
     * @param list<Violation> $violations
     */
    private function formatViolations(array $violations): string
    {
        $count = count($violations);
        $plural = 1 === $count ? 'violation' : 'violations';

        $lines = ["Failed asserting that the page is accessible. Found {$count} {$plural}:", ''];

        foreach ($violations as $index => $violation) {
            $num = $index + 1;
            $impact = $violation->impact ? " ({$violation->impact})" : '';

            $lines[] = "{$num}) {$violation->id}{$impact}";
            $lines[] = "   \"{$violation->help}\"";

            if (count($violation->nodes) > 0) {
                $lines[] = '   Impacted nodes:';
                foreach ($violation->nodes as $node) {
                    $selector = $node->getSelector();
                    $lines[] = "   -> {$selector}";
                }
            }

            $lines[] = "   Help: {$violation->helpUrl}";
            $lines[] = '';
        }

        return implode("\n", $lines);
    }
}
