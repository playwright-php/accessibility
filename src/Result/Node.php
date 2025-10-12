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

namespace Playwright\Accessibility\Result;

/**
 * Represents a single node (HTML element) affected by an accessibility rule.
 */
final readonly class Node
{
    /**
     * @param list<string>         $target         CSS selectors or other identifiers for the element
     * @param string|null          $html           HTML snippet of the element
     * @param string|null          $failureSummary Human-readable summary of why this node failed
     * @param array<string, mixed> $any            Additional data specific to the rule
     * @param array<string, mixed> $all            Additional data specific to the rule
     * @param array<string, mixed> $none           Additional data specific to the rule
     */
    public function __construct(
        public array $target,
        public ?string $html = null,
        public ?string $failureSummary = null,
        public array $any = [],
        public array $all = [],
        public array $none = [],
    ) {
    }

    /**
     * Create a Node from axe-core JSON data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $target = $data['target'] ?? [];
        $html = $data['html'] ?? null;
        $failureSummary = $data['failureSummary'] ?? null;
        $any = $data['any'] ?? [];
        $all = $data['all'] ?? [];
        $none = $data['none'] ?? [];

        assert(is_array($target), 'Target must be an array');
        assert(null === $html || is_string($html), 'HTML must be string or null');
        assert(null === $failureSummary || is_string($failureSummary), 'Failure summary must be string or null');
        assert(is_array($any), 'Any must be an array');
        assert(is_array($all), 'All must be an array');
        assert(is_array($none), 'None must be an array');

        /** @var list<string> $target */
        /** @var array<string, mixed> $any */
        /** @var array<string, mixed> $all */
        /** @var array<string, mixed> $none */
        return new self(
            target: $target,
            html: $html,
            failureSummary: $failureSummary,
            any: $any,
            all: $all,
            none: $none,
        );
    }

    /**
     * Get the primary CSS selector for this node.
     */
    public function getSelector(): string
    {
        return $this->target[0] ?? '';
    }
}
