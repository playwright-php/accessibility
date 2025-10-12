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
 * Common axe-core rule identifiers.
 *
 * This enum provides constants for frequently-used accessibility rules.
 * Note that axe-core has many more rules than listed here - you can still
 * use string literals for rules not included in this enum.
 *
 * @see https://github.com/dequelabs/axe-core/blob/master/doc/rule-descriptions.md
 */
enum RuleId: string
{
    /**
     * Elements must have sufficient color contrast.
     */
    case COLOR_CONTRAST = 'color-contrast';

    /**
     * Images must have alternate text.
     */
    case IMAGE_ALT = 'image-alt';

    /**
     * Form elements must have labels.
     */
    case LABEL = 'label';

    /**
     * Buttons must have discernible text.
     */
    case BUTTON_NAME = 'button-name';

    /**
     * Links must have discernible text.
     */
    case LINK_NAME = 'link-name';

    /**
     * Heading levels should only increase by one.
     */
    case HEADING_ORDER = 'heading-order';

    /**
     * Documents must have a title element.
     */
    case DOCUMENT_TITLE = 'document-title';

    /**
     * <html> element must have a lang attribute.
     */
    case HTML_HAS_LANG = 'html-has-lang';

    /**
     * lang attribute must have a valid value.
     */
    case VALID_LANG = 'valid-lang';

    /**
     * Landmark regions must have unique labels.
     */
    case LANDMARK_UNIQUE = 'landmark-unique';

    /**
     * Page must contain a level-one heading.
     */
    case PAGE_HAS_HEADING_ONE = 'page-has-heading-one';

    /**
     * ARIA attributes must conform to valid names.
     */
    case ARIA_VALID_ATTR = 'aria-valid-attr';

    /**
     * ARIA attributes must have valid values.
     */
    case ARIA_VALID_ATTR_VALUE = 'aria-valid-attr-value';

    /**
     * ARIA roles must be valid.
     */
    case ARIA_ROLES = 'aria-roles';

    /**
     * Ensures every ARIA input field has an accessible name.
     */
    case ARIA_INPUT_FIELD_NAME = 'aria-input-field-name';

    /**
     * IDs used in ARIA must be unique.
     */
    case DUPLICATE_ID_ARIA = 'duplicate-id-aria';

    /**
     * Convert enum value to string for use with axe-core API.
     */
    public function toString(): string
    {
        return $this->value;
    }
}
