# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.0.2-alpha] - 2020-12-23
### Added
- A shipment tracking service with the ability to track shipments by carrier code / tracking number or package id.

### Changed
- The underlying handling of info, warning, and error messages.
- Model classes are a directory down. E.g., `ShipEngine\Model\Address\Query` instead of `ShipEngine\Model\AddressQuery`.

## [0.0.1-alpha] - 2020-12-04
### Added
- The base ShipEngine client.
- An Addresses service with the ability to query, validate, and normalize an address.

[Unreleased]: https://github.com/ShipEngine/shipengine-php/compare/v0.0.2-alpha...HEAD
[0.0.2-alpha]: https://github.com/ShipEngine/shipengine-php/releases/tag/v0.0.2-alpha
[0.0.1-alpha]: https://github.com/ShipEngine/shipengine-php/releases/tag/v0.0.1-alpha
