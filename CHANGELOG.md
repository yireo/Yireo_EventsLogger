# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 27 November 2026
### Added
- Log every dispatched Magento event to `var/log/events.log` for debugging
- Admin setting (Stores > Configuration > Advanced > Yireo Events Logger) to enable logging, disabled by default
- Documentation of the configuration and a warning against enabling logging in production

### Changed
- Require `magento/framework` ^103.0 instead of any version and use the `OSL-3.0` license identifier
- Drop PHP 8.1 support, add PHP 8.5 support

### Security
- Sanitize event names before logging (skip non-string names, strip control characters) to prevent log injection
- Logging failures no longer break event dispatch
