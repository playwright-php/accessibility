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
 * Severity levels for accessibility violations.
 *
 * These represent the impact a violation has on users with disabilities.
 *
 * @see https://github.com/dequelabs/axe-core/blob/master/doc/API.md#results-object
 */
enum ImpactLevel: string
{
    /**
     * Critical impact - severely impacts accessibility.
     * Must be fixed immediately.
     */
    case CRITICAL = 'critical';

    /**
     * Serious impact - significantly impacts accessibility.
     * Should be fixed as soon as possible.
     */
    case SERIOUS = 'serious';

    /**
     * Moderate impact - impacts accessibility for some users.
     * Should be fixed in a reasonable timeframe.
     */
    case MODERATE = 'moderate';

    /**
     * Minor impact - minimal accessibility impact.
     * Can be addressed as time permits.
     */
    case MINOR = 'minor';

    /**
     * Convert enum value to string.
     */
    public function toString(): string
    {
        return $this->value;
    }

    /**
     * Get a human-readable description of the impact level.
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::CRITICAL => 'Critical - severely impacts accessibility',
            self::SERIOUS => 'Serious - significantly impacts accessibility',
            self::MODERATE => 'Moderate - impacts accessibility for some users',
            self::MINOR => 'Minor - minimal accessibility impact',
        };
    }
}
