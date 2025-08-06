# Address Parse

Address Parse is a PHP utility library designed to extract and validate address components from raw text. Whether you're processing user input, scraping addresses from documents, or standardizing address formats, this library provides robust tools to parse addresses into their constituent parts such as street, city, state, postal code, and country.

## Features

- **Flexible Parsing:** Handles a variety of address formats.
- **Component Extraction:** Breaks down addresses into street, city, state, postal code, and country.
- **Validation:** Validates address components for correctness.
- **Extensible:** Easy to add support for new address formats or regions.

## Installation

You can install Address Parse via Composer:

```bash
composer require viveknadig/address-parse
```

Or clone this repository:

```bash
git clone https://github.com/viveknadig/address-parse.git
cd address-parse
```

## Usage

```php
<?php

require 'vendor/autoload.php';

use Viveknadig\AddressParse\AddressParser;

$address = "221B Baker Street, London NW1 6XE, UK";
$parser = new AddressParser();
$components = $parser->parse($address);

print_r($components);
// Output: Array
// (
//     [street] => 221B Baker Street
//     [city] => London
//     [postal_code] => NW1 6XE
//     [country] => UK
// )
```

## API Reference

### `AddressParser::parse(string $address): array`

Parses the given address string and returns an associative array with keys:
- `street`
- `city`
- `state` (if available)
- `postal_code`
- `country`

### `AddressParser::validate(array $components): bool`

Validates the given address components.

## Contributing

Contributions are welcome! Please open an issue or submit a pull request for any improvements or bug fixes.

1. Fork the repo
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License.

## Authors

- [viveknadig](https://github.com/viveknadig)

## Acknowledgements

- Inspired by real-world address parsing challenges.
- Thanks to contributors and testers!
