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
use PHPUnit\Framework\TestCase;
use Playwright\Accessibility\Result\Node;

#[CoversClass(Node::class)]
final class NodeTest extends TestCase
{
    public function testCanCreateNodeFromArray(): void
    {
        $data = [
            'target' => ['#my-element', 'body > div'],
            'html' => '<div id="my-element">Test</div>',
            'failureSummary' => 'Fix this issue',
            'any' => ['check1' => 'value1'],
            'all' => ['check2' => 'value2'],
            'none' => ['check3' => 'value3'],
        ];

        $node = Node::fromArray($data);

        $this->assertSame(['#my-element', 'body > div'], $node->target);
        $this->assertSame('<div id="my-element">Test</div>', $node->html);
        $this->assertSame('Fix this issue', $node->failureSummary);
        $this->assertSame(['check1' => 'value1'], $node->any);
        $this->assertSame(['check2' => 'value2'], $node->all);
        $this->assertSame(['check3' => 'value3'], $node->none);
    }

    public function testCanCreateNodeWithMinimalData(): void
    {
        $data = [
            'target' => ['#element'],
        ];

        $node = Node::fromArray($data);

        $this->assertSame(['#element'], $node->target);
        $this->assertNull($node->html);
        $this->assertNull($node->failureSummary);
        $this->assertSame([], $node->any);
        $this->assertSame([], $node->all);
        $this->assertSame([], $node->none);
    }

    public function testGetSelectorReturnsFirstTarget(): void
    {
        $node = new Node(
            target: ['#my-element', 'body > div'],
        );

        $this->assertSame('#my-element', $node->getSelector());
    }

    public function testGetSelectorReturnsEmptyStringWhenNoTargets(): void
    {
        $node = new Node(target: []);

        $this->assertSame('', $node->getSelector());
    }
}
