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

use Playwright\Page\PageInterface;

/**
 * Fluent builder for configuring and running axe-core accessibility analysis.
 */
final class AxeBuilder
{
    private ?string $context = null;

    /** @var list<string> */
    private array $excluded = [];

    /** @var list<string> */
    private array $tags = [];

    /** @var list<string> */
    private array $disabledRules = [];

    public function __construct(
        private readonly PageInterface $page,
    ) {
    }

    /**
     * Analyze only a specific part of the page.
     *
     * @param string $selector CSS selector to scope the analysis
     */
    public function within(string $selector): self
    {
        $this->context = $selector;

        return $this;
    }

    /**
     * Exclude specific elements from the analysis.
     *
     * @param string $selector CSS selector to exclude
     */
    public function exclude(string $selector): self
    {
        $this->excluded[] = $selector;

        return $this;
    }

    /**
     * Run only rules with specific tags (e.g., 'wcag2a', 'wcag21aa').
     *
     * @param list<WcagTag|string> $tags List of tags to include (accepts WcagTag enums or strings)
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
     * Disable specific rules by ID.
     *
     * @param list<RuleId|string> $rules List of rule IDs to disable (accepts RuleId enums or strings)
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
     * Run the accessibility analysis and return results.
     */
    public function analyze(): AxeResults
    {
        // Load axe-core script
        $axeSource = $this->loadAxeCore();

        // Build the context and options for axe.run()
        $context = $this->buildContext();
        $options = $this->buildOptions();

        // Execute axe-core in the browser
        $script = $this->buildScript($axeSource, $context, $options);
        $result = $this->page->evaluate($script);
        assert(is_array($result), 'Playwright evaluate() should return an array from axe.run()');

        // Convert to typed results
        /** @var array<string, mixed> $result */
        return AxeResults::fromArray($result);
    }

    /**
     * Load the bundled axe-core.js script.
     */
    private function loadAxeCore(): string
    {
        $path = __DIR__.'/resources/axe-core.js';

        if (!file_exists($path)) {
            throw new \RuntimeException('axe-core.js not found. Please ensure it is bundled in src/resources/axe-core.js');
        }

        $content = file_get_contents($path);

        if (false === $content) {
            throw new \RuntimeException('Failed to read axe-core.js');
        }

        return $content;
    }

    /**
     * Build the context parameter for axe.run().
     *
     * @return string|array<string, mixed>
     */
    private function buildContext(): string|array
    {
        // If we have exclusions, we need to build an object
        if (count($this->excluded) > 0) {
            // When we have exclusions, we need to specify include as well
            // If no specific context is set, use 'html' as the root selector
            $include = $this->context ?? 'html';

            return [
                'include' => [[$include]],
                'exclude' => array_map(fn (string $sel) => [$sel], $this->excluded),
            ];
        }

        // Otherwise, just return the context selector (or 'document' by default)
        return $this->context ?? 'document';
    }

    /**
     * Build the options parameter for axe.run().
     *
     * @return array<string, mixed>
     */
    private function buildOptions(): array
    {
        $options = [];

        if (count($this->tags) > 0) {
            $options['runOnly'] = [
                'type' => 'tag',
                'values' => $this->tags,
            ];
        }

        if (count($this->disabledRules) > 0) {
            $options['rules'] = [];
            foreach ($this->disabledRules as $ruleId) {
                $options['rules'][$ruleId] = ['enabled' => false];
            }
        }

        return $options;
    }

    /**
     * Build the JavaScript to execute in the browser.
     *
     * @param string                      $axeSource The axe-core.js source code
     * @param string|array<string, mixed> $context   The context parameter
     * @param array<string, mixed>        $options   The options parameter
     */
    private function buildScript(string $axeSource, string|array $context, array $options): string
    {
        // Determine if we should pass context or use default
        $useDefaultContext = is_string($context) && 'document' === $context;

        $contextParam = $useDefaultContext ? '' : json_encode($context).',';
        $optionsParam = empty($options) ? '' : json_encode($options);

        return <<<JS
(async () => {
    // Inject axe-core if not already present
    if (typeof axe === 'undefined') {
        {$axeSource}
    }

    // Run axe with the specified context and options
    const results = await axe.run({$contextParam} {$optionsParam});

    return results;
})();
JS;
    }
}
