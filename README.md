<div align="center">
    <img src="https://github.com/playwright-php/.github/raw/main/profile/playwright-php.png" alt="Playwright PHP" />

&nbsp; ![PHP Version](https://img.shields.io/badge/PHP-8.2-05971B?labelColor=09161E&color=1D8D23&logoColor=FFFFFF)
&nbsp; ![CI](https://img.shields.io/github/actions/workflow/status/playwright-php/accessibility/CI.yml?branch=main&label=Tests&color=1D8D23&labelColor=09161E&logoColor=FFFFFF)
&nbsp; ![Release](https://img.shields.io/github/v/release/playwright-php/accessibility?label=Stable&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)
&nbsp; ![License](https://img.shields.io/github/license/playwright-php/accessibility?label=License&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)

</div>

# Playwright PHP - Accessibility

Perform real **accessibility audits** on web pages using [Playwright PHP](https://github.com/playwright-php/playwright) and [axe-core](https://github.com/dequelabs/axe-core),  
checking for **WCAG**, **ARIA**, color contrast, and best-practice compliance.

## Installation

This package relies on **Playwright PHP** - to install it, follow the instructions in [Playwright PHP’s installation guide](https://github.com/playwright-php/playwright#installation).

```bash
composer require --dev playwright-php/accessibility
````

## Usage

### Basic Analysis

```php
use Playwright\Accessibility\AxeBuilder;

$builder = new AxeBuilder($page);
$results = $builder->analyze();

if ($results->hasViolations()) {
    foreach ($results->violations as $violation) {
        echo "{$violation->id}: {$violation->help}\n";
    }
}
```

### PHPUnit Integration

```php
use Playwright\Accessibility\AssertsAccessibility;

class MyTest extends TestCase
{
    use AssertsAccessibility;

    public function testPageIsAccessible(): void
    {
        $page->goto('https://example.com');
        $this->assertIsAccessible($page);
    }
}
```

### Advanced Configuration

```php
// Scope to specific regions
$builder->within('#main-content')->analyze();

// Filter by WCAG level
$builder->withTags([WcagTag::WCAG_2_1_AA])->analyze();

// Disable specific rules
$builder->withoutRules([RuleId::COLOR_CONTRAST])->analyze();

// Exclude elements
$builder->exclude('.advertisement')->analyze();
```

## License

This package is released by the [Playwright PHP](https://playwright-php.dev) project
under the **MIT License**. See the [LICENSE](LICENSE) file for details.
