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
 * Base class for accessibility check results.
 */
abstract readonly class Result
{
    /**
     * @param string       $id          Unique identifier of the accessibility rule
     * @param string       $description Human-readable description of the rule
     * @param string       $help        Brief help text for the rule
     * @param string       $helpUrl     URL to detailed documentation
     * @param list<Node>   $nodes       List of affected nodes
     * @param list<string> $tags        Tags/categories this rule belongs to (e.g., 'wcag2a', 'wcag21aa')
     * @param string|null  $impact      Impact level (null for passes)
     */
    final public function __construct(
        public string $id,
        public string $description,
        public string $help,
        public string $helpUrl,
        public array $nodes,
        public array $tags,
        public ?string $impact = null,
    ) {
    }

    /**
     * Create a Result from axe-core JSON data.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        /** @var list<array<string, mixed>> $nodesData */
        $nodesData = $data['nodes'] ?? [];

        $nodes = array_map(Node::fromArray(...), $nodesData);

        $id = $data['id'] ?? '';
        $description = $data['description'] ?? '';
        $help = $data['help'] ?? '';
        $helpUrl = $data['helpUrl'] ?? '';
        $tags = $data['tags'] ?? [];
        $impact = $data['impact'] ?? null;

        assert(is_string($id), 'ID must be a string');
        assert(is_string($description), 'Description must be a string');
        assert(is_string($help), 'Help must be a string');
        assert(is_string($helpUrl), 'Help URL must be a string');
        assert(is_array($tags), 'Tags must be an array');
        assert(null === $impact || is_string($impact), 'Impact must be string or null');

        /** @var list<string> $tags */
        return new static(
            id: $id,
            description: $description,
            help: $help,
            helpUrl: $helpUrl,
            nodes: $nodes,
            tags: $tags,
            impact: $impact,
        );
    }
}
