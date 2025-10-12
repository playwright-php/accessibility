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

use Playwright\Accessibility\Result\Inapplicable;
use Playwright\Accessibility\Result\Incomplete;
use Playwright\Accessibility\Result\Pass;
use Playwright\Accessibility\Result\Violation;

/**
 * Contains the results of an axe-core accessibility analysis.
 */
final readonly class AxeResults
{
    /**
     * @param list<Violation>    $violations   Rules that failed
     * @param list<Pass>         $passes       Rules that passed
     * @param list<Incomplete>   $incomplete   Rules that could not be fully tested
     * @param list<Inapplicable> $inapplicable Rules that were not applicable
     * @param string|null        $url          The URL of the page that was tested
     * @param string|null        $timestamp    ISO timestamp of when the test was run
     */
    public function __construct(
        public array $violations = [],
        public array $passes = [],
        public array $incomplete = [],
        public array $inapplicable = [],
        public ?string $url = null,
        public ?string $timestamp = null,
    ) {
    }

    /**
     * Create AxeResults from axe-core JSON output.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        /** @var list<array<string, mixed>> $violationsData */
        $violationsData = $data['violations'] ?? [];
        /** @var list<array<string, mixed>> $passesData */
        $passesData = $data['passes'] ?? [];
        /** @var list<array<string, mixed>> $incompleteData */
        $incompleteData = $data['incomplete'] ?? [];
        /** @var list<array<string, mixed>> $inapplicableData */
        $inapplicableData = $data['inapplicable'] ?? [];

        $url = $data['url'] ?? null;
        $timestamp = $data['timestamp'] ?? null;

        assert(null === $url || is_string($url), 'URL must be string or null');
        assert(null === $timestamp || is_string($timestamp), 'Timestamp must be string or null');

        return new self(
            violations: array_values(array_map(Violation::fromArray(...), $violationsData)),
            passes: array_values(array_map(Pass::fromArray(...), $passesData)),
            incomplete: array_values(array_map(Incomplete::fromArray(...), $incompleteData)),
            inapplicable: array_values(array_map(Inapplicable::fromArray(...), $inapplicableData)),
            url: $url,
            timestamp: $timestamp,
        );
    }

    /**
     * Check if the page has any violations.
     */
    public function hasViolations(): bool
    {
        return count($this->violations) > 0;
    }

    /**
     * Get the total number of violations.
     */
    public function getViolationCount(): int
    {
        return count($this->violations);
    }
}
