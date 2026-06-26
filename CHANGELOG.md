# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## v1.0.1 - 2026-02-04

<!-- Release notes generated using configuration in .github/release.yml at main -->
**Full Changelog**: https://github.com/KYNetCode/wpzylos-cli-devtool/compare/v1.0.0...v1.0.1

## v1.0.0 - 2026-02-01

First stable release of wpzylos-cli-devtool

## [Unreleased]

### Added

- App-aware `make:menu` stub that boots with `ApplicationInterface`, registers hooks through `HookManager`, and supports Vite/admin mount options.
- Expanded `make:shortcode` generation for WPZylos services, Vite assets, Gutenberg blocks, Elementor widgets, and WPBakery mappings.

### Changed

- Generator context resolution now prefers the configured database prefix when available.

### Deprecated

### Removed

### Fixed

### Security
