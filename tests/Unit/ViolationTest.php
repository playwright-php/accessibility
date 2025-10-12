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
use Playwright\Accessibility\Result\Result;
use Playwright\Accessibility\Result\Violation;

#[CoversClass(Violation::class)]
#[CoversClass(Result::class)]
#[CoversClass(Node::class)]
final class ViolationTest extends TestCase
{
    public function testCanCreateViolationFromArray(): void
    {
        $data = [
            'id' => 'color-contrast',
            'description' => 'Ensures the contrast between foreground and background colors',
            'help' => 'Elements must have sufficient color contrast',
            'helpUrl' => 'https://dequeuniversity.com/rules/axe/4.4/color-contrast',
            'impact' => 'serious',
            'tags' => ['wcag2aa', 'wcag143'],
            'nodes' => [
                [
                    'target' => ['.text-muted'],
                    'html' => '<span class="text-muted">Low contrast text</span>',
                    'failureSummary' => 'Fix any of the following:\n  Element has insufficient color contrast',
                ],
            ],
        ];

        $violation = Violation::fromArray($data);

        $this->assertSame('color-contrast', $violation->id);
        $this->assertSame('Ensures the contrast between foreground and background colors', $violation->description);
        $this->assertSame('Elements must have sufficient color contrast', $violation->help);
        $this->assertSame('https://dequeuniversity.com/rules/axe/4.4/color-contrast', $violation->helpUrl);
        $this->assertSame('serious', $violation->impact);
        $this->assertSame(['wcag2aa', 'wcag143'], $violation->tags);
        $this->assertCount(1, $violation->nodes);
        $this->assertSame('.text-muted', $violation->nodes[0]->getSelector());
    }

    public function testCanCreateViolationWithMultipleNodes(): void
    {
        $data = [
            'id' => 'image-alt',
            'description' => 'Ensures <img> elements have alternate text',
            'help' => 'Images must have alternate text',
            'helpUrl' => 'https://dequeuniversity.com/rules/axe/4.4/image-alt',
            'impact' => 'critical',
            'tags' => ['wcag2a'],
            'nodes' => [
                ['target' => ['img.logo']],
                ['target' => ['img.banner']],
                ['target' => ['img.icon']],
            ],
        ];

        $violation = Violation::fromArray($data);

        $this->assertCount(3, $violation->nodes);
        $this->assertSame('img.logo', $violation->nodes[0]->getSelector());
        $this->assertSame('img.banner', $violation->nodes[1]->getSelector());
        $this->assertSame('img.icon', $violation->nodes[2]->getSelector());
    }
}
