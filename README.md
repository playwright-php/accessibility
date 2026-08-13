<div align="center">
<a href="https://github.com/playwright-php"><img src="https://github.com/playwright-php/.github/raw/main/profile/playwright-php.png" alt="Playwright PHP" /></a>

&nbsp; ![PHP Version](https://img.shields.io/badge/PHP-8.2+-05971B?labelColor=09161E&color=1D8D23&logoColor=FFFFFF)
&nbsp; ![CI](https://img.shields.io/github/actions/workflow/status/playwright-php/accessibility/CI.yml?branch=main&label=Tests&color=1D8D23&labelColor=09161E&logoColor=FFFFFF)
&nbsp; [![Release](https://img.shields.io/github/v/release/playwright-php/accessibility?label=Stable&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)](https://packagist.org/packages/playwright-php/accessibility)
&nbsp; ![License](https://img.shields.io/github/license/playwright-php/accessibility?label=License&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)

</div>

# Playwright PHP Accessibility

Run [axe-core](https://github.com/dequelabs/axe-core) checks in pages controlled
by [Playwright PHP](https://github.com/playwright-php/playwright), inspect the
results as PHP objects, or fail PHPUnit tests when violations are found.

## Installation

The package requires PHP 8.2 or later and Playwright PHP 1.x.

```bash
composer require --dev playwright-php/accessibility
vendor/bin/playwright-install --browsers
```

## Quick Start

Use the PHPUnit trait with Playwright PHP's test case:

```php
<?php

use Playwright\Accessibility\AssertsAccessibility;
use Playwright\Testing\PlaywrightTestCase;

final class AccessibilityTest extends PlaywrightTestCase
{
    use AssertsAccessibility;

    public function test_homepage_has_no_detected_violations(): void
    {
        $this->page->goto('https://example.com');

        $this->assertIsAccessible($this->page);
    }
}
```

## Configuring an Audit

`AxeBuilder` can limit the scan to a region, select standards, exclude elements,
or disable rules:

```php
use Playwright\Accessibility\AxeBuilder;
use Playwright\Accessibility\RuleId;
use Playwright\Accessibility\WcagTag;

$results = (new AxeBuilder($page))
    ->within('#main-content')
    ->exclude('.third-party-widget')
    ->withTags([WcagTag::WCAG_2_1_AA])
    ->withoutRules([RuleId::COLOR_CONTRAST])
    ->analyze();

foreach ($results->violations as $violation) {
    echo $violation->id.': '.$violation->help.PHP_EOL;
}
```

`assertIsAccessible()` accepts a page, a configured builder, or an existing
result object.

## Limits

Automated axe checks detect only part of the accessibility problems a user may
encounter. A passing result does not establish WCAG conformance and does not
replace keyboard, screen-reader, zoom, motion, or usability testing.

The audit covers the page state at the time it runs. Navigate, authenticate,
open dialogs, and trigger dynamic states before making the assertion.

## Documentation

- [Playwright PHP Getting Started](https://github.com/playwright-php/playwright/blob/main/docs/guide/getting-started.md)
- [axe-core rule descriptions](https://github.com/dequelabs/axe-core/blob/master/doc/rule-descriptions.md)

## Contributing

Install dependencies and browsers, then run code style, static analysis, and
the test suite:

```bash
composer install
vendor/bin/playwright-install --browsers
vendor/bin/php-cs-fixer fix --dry-run --diff
vendor/bin/phpstan analyse --memory-limit=-1
vendor/bin/phpunit
```

## License

This package is released under the [MIT License](LICENSE).
