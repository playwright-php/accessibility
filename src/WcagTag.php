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

/**
 * WCAG (Web Content Accessibility Guidelines) conformance level tags.
 *
 * These tags can be used with AxeBuilder::withTags() to run only rules
 * associated with a specific accessibility standard.
 *
 * @see https://www.w3.org/WAI/WCAG21/quickref/
 */
enum WcagTag: string
{
    /**
     * WCAG 2.0 Level A - Minimum level of conformance.
     */
    case WCAG_2_0_A = 'wcag2a';

    /**
     * WCAG 2.0 Level AA - Mid-range level, recommended for most websites.
     */
    case WCAG_2_0_AA = 'wcag2aa';

    /**
     * WCAG 2.0 Level AAA - Highest level of conformance.
     */
    case WCAG_2_0_AAA = 'wcag2aaa';

    /**
     * WCAG 2.1 Level A - Includes 2.0 Level A + new 2.1 requirements.
     */
    case WCAG_2_1_A = 'wcag21a';

    /**
     * WCAG 2.1 Level AA - Includes 2.0 Level AA + new 2.1 requirements.
     */
    case WCAG_2_1_AA = 'wcag21aa';

    /**
     * WCAG 2.1 Level AAA - Includes 2.0 Level AAA + new 2.1 requirements.
     */
    case WCAG_2_1_AAA = 'wcag21aaa';

    /**
     * WCAG 2.2 Level A - Includes 2.1 Level A + new 2.2 requirements.
     */
    case WCAG_2_2_A = 'wcag22a';

    /**
     * WCAG 2.2 Level AA - Includes 2.1 Level AA + new 2.2 requirements.
     */
    case WCAG_2_2_AA = 'wcag22aa';

    /**
     * Best Practices - Additional checks beyond WCAG compliance.
     */
    case BEST_PRACTICE = 'best-practice';

    /**
     * Experimental rules - Not part of official WCAG standards.
     */
    case EXPERIMENTAL = 'experimental';

    /**
     * Section 508 - US federal accessibility standard.
     */
    case SECTION_508 = 'section508';

    /**
     * Convert enum value to string for use with axe-core API.
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Get all WCAG 2.0 tags.
     *
     * @return list<self>
     */
    public static function wcag20(): array
    {
        return [self::WCAG_2_0_A, self::WCAG_2_0_AA, self::WCAG_2_0_AAA];
    }

    /**
     * Get all WCAG 2.1 tags.
     *
     * @return list<self>
     */
    public static function wcag21(): array
    {
        return [self::WCAG_2_1_A, self::WCAG_2_1_AA, self::WCAG_2_1_AAA];
    }

    /**
     * Get all WCAG 2.2 tags.
     *
     * @return list<self>
     */
    public static function wcag22(): array
    {
        return [self::WCAG_2_2_A, self::WCAG_2_2_AA];
    }
}
