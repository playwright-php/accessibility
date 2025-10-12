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

namespace Playwright\Accessibility\Testing;

use Playwright\Accessibility\AxeResults;
use Playwright\Accessibility\ImpactLevel;
use Playwright\Accessibility\Result\Inapplicable;
use Playwright\Accessibility\Result\Incomplete;
use Playwright\Accessibility\Result\Node;
use Playwright\Accessibility\Result\Pass;
use Playwright\Accessibility\Result\Violation;
use Playwright\Accessibility\RuleId;
use Playwright\Accessibility\WcagTag;

/**
 * Mock AxeBuilder for testing without a real browser.
 *
 * This allows you to simulate accessibility test results in your unit tests
 * without needing to run Playwright or have axe-core available.
 */
final class MockAxeBuilder
{
    /** @var list<array{id: string, impact: string, nodes: list<string>}> */
    private array $violations = [];

    /** @var list<string> */
    private array $passes = [];

    /** @var list<string> */
    private array $incomplete = [];

    /** @var list<string> */
    private array $inapplicable = [];

    /**
     * @phpstan-ignore property.onlyWritten (API compatibility - not used in mock)
     */
    private ?string $context = null;

    /**
     * @var list<string>
     *
     * @phpstan-ignore property.onlyWritten (API compatibility - not used in mock)
     */
    private array $excluded = [];

    /**
     * @var list<string>
     *
     * @phpstan-ignore property.onlyWritten (API compatibility - not used in mock)
     */
    private array $tags = [];

    /** @var list<string> */
    private array $disabledRules = [];

    private function __construct()
    {
    }

    /**
     * Create a new mock builder.
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Add a violation to the mock results.
     *
     * @param RuleId|string      $ruleId    The rule ID (accepts RuleId enum or string)
     * @param ImpactLevel|string $impact    Impact level (accepts ImpactLevel enum or string)
     * @param list<string>       $selectors CSS selectors of affected elements
     * @param string|null        $help      Optional help text (generated if not provided)
     */
    public function withViolation(
        RuleId|string $ruleId,
        ImpactLevel|string $impact = 'serious',
        array $selectors = [],
        ?string $help = null,
    ): self {
        $ruleIdString = $ruleId instanceof RuleId ? $ruleId->value : $ruleId;
        $impactString = $impact instanceof ImpactLevel ? $impact->value : $impact;

        $this->violations[] = [
            'id' => $ruleIdString,
            'impact' => $impactString,
            'nodes' => $selectors,
            'help' => $help ?? $this->generateHelpText($ruleIdString),
        ];

        return $this;
    }

    /**
     * Add a passing rule to the mock results.
     */
    public function withPass(string $ruleId): self
    {
        $this->passes[] = $ruleId;

        return $this;
    }

    /**
     * Add an incomplete rule to the mock results.
     */
    public function withIncomplete(string $ruleId): self
    {
        $this->incomplete[] = $ruleId;

        return $this;
    }

    /**
     * Add an inapplicable rule to the mock results.
     */
    public function withInapplicable(string $ruleId): self
    {
        $this->inapplicable[] = $ruleId;

        return $this;
    }

    /**
     * Simulate an accessible page with no violations.
     */
    public function withNoViolations(): self
    {
        $this->violations = [];
        $this->withPass('document-title');
        $this->withPass('html-has-lang');
        $this->withPass('landmark-one-main');

        return $this;
    }

    /**
     * Fluent API compatibility: within() (no-op in mock).
     */
    public function within(string $selector): self
    {
        $this->context = $selector;

        return $this;
    }

    /**
     * Fluent API compatibility: exclude() (no-op in mock).
     */
    public function exclude(string $selector): self
    {
        $this->excluded[] = $selector;

        return $this;
    }

    /**
     * Fluent API compatibility: withTags() (no-op in mock).
     *
     * @param list<WcagTag|string> $tags
     */
    public function withTags(array $tags): self
    {
        $this->tags = array_map(
            fn (WcagTag|string $tag) => $tag instanceof WcagTag ? $tag->value : $tag,
            $tags
        );

        return $this;
    }

    /**
     * Fluent API compatibility: withoutRules() (filters out violations in mock).
     *
     * @param list<RuleId|string> $rules
     */
    public function withoutRules(array $rules): self
    {
        $this->disabledRules = array_map(
            fn (RuleId|string $rule) => $rule instanceof RuleId ? $rule->value : $rule,
            $rules
        );

        return $this;
    }

    /**
     * Generate the mock AxeResults.
     */
    public function analyze(): AxeResults
    {
        // Filter out disabled rules
        $violations = array_filter(
            $this->violations,
            fn (array $v) => !\in_array($v['id'], $this->disabledRules, true)
        );

        return new AxeResults(
            violations: array_values(array_map($this->createViolation(...), $violations)),
            passes: array_map($this->createPass(...), $this->passes),
            incomplete: array_map($this->createIncomplete(...), $this->incomplete),
            inapplicable: array_map($this->createInapplicable(...), $this->inapplicable),
            url: 'http://mock-test.local',
            timestamp: date('c'),
        );
    }

    /**
     * @param array{id: string, impact: string, nodes: list<string>, help?: string} $data
     */
    private function createViolation(array $data): Violation
    {
        $nodes = array_map(
            fn (string $selector) => new Node(target: [$selector]),
            $data['nodes']
        );

        return new Violation(
            id: $data['id'],
            description: $this->generateDescription($data['id']),
            help: $data['help'] ?? $this->generateHelpText($data['id']),
            helpUrl: "https://dequeuniversity.com/rules/axe/4.4/{$data['id']}",
            nodes: $nodes,
            tags: $this->inferTags($data['id']),
            impact: $data['impact'],
        );
    }

    private function createPass(string $id): Pass
    {
        return new Pass(
            id: $id,
            description: $this->generateDescription($id),
            help: $this->generateHelpText($id),
            helpUrl: "https://dequeuniversity.com/rules/axe/4.4/{$id}",
            nodes: [],
            tags: $this->inferTags($id),
        );
    }

    private function createIncomplete(string $id): Incomplete
    {
        return new Incomplete(
            id: $id,
            description: $this->generateDescription($id),
            help: $this->generateHelpText($id),
            helpUrl: "https://dequeuniversity.com/rules/axe/4.4/{$id}",
            nodes: [],
            tags: $this->inferTags($id),
        );
    }

    private function createInapplicable(string $id): Inapplicable
    {
        return new Inapplicable(
            id: $id,
            description: $this->generateDescription($id),
            help: $this->generateHelpText($id),
            helpUrl: "https://dequeuniversity.com/rules/axe/4.4/{$id}",
            nodes: [],
            tags: $this->inferTags($id),
        );
    }

    private function generateDescription(string $ruleId): string
    {
        return ucfirst(str_replace('-', ' ', $ruleId));
    }

    private function generateHelpText(string $ruleId): string
    {
        $descriptions = [
            'color-contrast' => 'Elements must have sufficient color contrast',
            'image-alt' => 'Images must have alternate text',
            'label' => 'Form elements must have labels',
            'button-name' => 'Buttons must have discernible text',
            'link-name' => 'Links must have discernible text',
            'document-title' => 'Documents must have a title',
            'html-has-lang' => 'HTML element must have a lang attribute',
            'landmark-one-main' => 'Document must have one main landmark',
        ];

        return $descriptions[$ruleId] ?? ucfirst(str_replace('-', ' ', $ruleId));
    }

    /**
     * @return list<string>
     */
    private function inferTags(string $ruleId): array
    {
        // Basic heuristic for common rules
        $wcag2a = ['image-alt', 'label', 'document-title', 'html-has-lang'];
        $wcag2aa = ['color-contrast', 'link-name'];

        $tags = ['best-practice'];

        if (\in_array($ruleId, $wcag2a, true)) {
            $tags[] = 'wcag2a';
            $tags[] = 'wcag21a';
        }

        if (\in_array($ruleId, $wcag2aa, true)) {
            $tags[] = 'wcag2aa';
            $tags[] = 'wcag21aa';
        }

        return $tags;
    }
}
