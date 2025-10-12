# Bundled Resources

## axe-core.js

**Version:** 4.10.2
**Size:** ~540KB (minified)
**License:** Mozilla Public License 2.0
**Source:** https://github.com/dequelabs/axe-core

### About

axe-core is the industry-standard accessibility testing engine by Deque Systems. It runs entirely in the browser and evaluates web pages against WCAG 2.0, WCAG 2.1, WCAG 2.2, and other accessibility standards.

### Why Bundled?

This file is bundled with the package to:
- Eliminate external dependencies (no CDN required)
- Ensure consistent behavior across environments
- Work offline and in isolated environments
- Guarantee version compatibility

### Usage

The `AxeBuilder` class automatically loads and injects this script into the browser when `analyze()` is called. You don't need to manually include it.

```php
use Playwright\Accessibility\AxeBuilder;

$builder = new AxeBuilder($page);
$results = $builder->analyze(); // axe-core.js is injected automatically
```

### Updating

To update to a newer version of axe-core:

1. Visit https://github.com/dequelabs/axe-core/releases
2. Download the latest `axe.min.js` from the release assets
3. Replace this file: `src/resources/axe-core.js`
4. Update the version number in this README
5. Run the test suite to ensure compatibility

```bash
# Or download via curl:
curl -L -o src/resources/axe-core.js \
  https://cdnjs.cloudflare.com/ajax/libs/axe-core/4.10.2/axe.min.js
```

### License Compliance

axe-core is licensed under the Mozilla Public License 2.0 (MPL-2.0), which allows:
- ✅ Commercial use
- ✅ Distribution
- ✅ Modification
- ✅ Private use

The MPL-2.0 requires that modifications to the axe-core source code itself be released under the same license. However, this package does not modify axe-core; it simply bundles and uses it as-is.

For full license details, see: https://github.com/dequelabs/axe-core/blob/develop/LICENSE
