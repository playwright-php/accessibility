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
use Playwright\Accessibility\ImpactLevel;
use Playwright\Accessibility\RuleId;
use Playwright\Accessibility\WcagTag;

#[CoversClass(WcagTag::class)]
#[CoversClass(RuleId::class)]
#[CoversClass(ImpactLevel::class)]
class EnumsTest extends TestCase
{
    public function testWcagTagsHaveCorrectValues(): void
    {
        $this->assertSame('wcag2a', WcagTag::WCAG_2_0_A->value);
        $this->assertSame('wcag2aa', WcagTag::WCAG_2_0_AA->value);
        $this->assertSame('wcag21a', WcagTag::WCAG_2_1_A->value);
        $this->assertSame('wcag21aa', WcagTag::WCAG_2_1_AA->value);
        $this->assertSame('wcag22a', WcagTag::WCAG_2_2_A->value);
        $this->assertSame('wcag22aa', WcagTag::WCAG_2_2_AA->value);
        $this->assertSame('best-practice', WcagTag::BEST_PRACTICE->value);
        $this->assertSame('section508', WcagTag::SECTION_508->value);
    }

    public function testWcagTagToStringConvertsToValue(): void
    {
        $tag = WcagTag::WCAG_2_1_AA;
        $this->assertSame('wcag21aa', $tag->toString());
    }

    public function testWcagTagGroupingMethods(): void
    {
        $wcag20 = WcagTag::wcag20();
        $this->assertCount(3, $wcag20);
        $this->assertContains(WcagTag::WCAG_2_0_A, $wcag20);
        $this->assertContains(WcagTag::WCAG_2_0_AA, $wcag20);

        $wcag21 = WcagTag::wcag21();
        $this->assertCount(3, $wcag21);
        $this->assertContains(WcagTag::WCAG_2_1_A, $wcag21);

        $wcag22 = WcagTag::wcag22();
        $this->assertCount(2, $wcag22);
        $this->assertContains(WcagTag::WCAG_2_2_A, $wcag22);
    }

    public function testRuleIdHasCommonRules(): void
    {
        $this->assertSame('color-contrast', RuleId::COLOR_CONTRAST->value);
        $this->assertSame('image-alt', RuleId::IMAGE_ALT->value);
        $this->assertSame('label', RuleId::LABEL->value);
        $this->assertSame('button-name', RuleId::BUTTON_NAME->value);
        $this->assertSame('link-name', RuleId::LINK_NAME->value);
        $this->assertSame('heading-order', RuleId::HEADING_ORDER->value);
    }

    public function testRuleIdToStringConvertsToValue(): void
    {
        $rule = RuleId::COLOR_CONTRAST;
        $this->assertSame('color-contrast', $rule->toString());
    }

    public function testImpactLevelHasAllLevels(): void
    {
        $this->assertSame('critical', ImpactLevel::CRITICAL->value);
        $this->assertSame('serious', ImpactLevel::SERIOUS->value);
        $this->assertSame('moderate', ImpactLevel::MODERATE->value);
        $this->assertSame('minor', ImpactLevel::MINOR->value);
    }

    public function testImpactLevelToStringConvertsToValue(): void
    {
        $impact = ImpactLevel::SERIOUS;
        $this->assertSame('serious', $impact->toString());
    }

    public function testImpactLevelDescriptions(): void
    {
        $this->assertStringContainsString('severely', ImpactLevel::CRITICAL->getDescription());
        $this->assertStringContainsString('significantly', ImpactLevel::SERIOUS->getDescription());
        $this->assertStringContainsString('some users', ImpactLevel::MODERATE->getDescription());
        $this->assertStringContainsString('minimal', ImpactLevel::MINOR->getDescription());
    }
}
