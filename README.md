# Yireo_EventsLogger

<!-- badges.specs.start -->
![Magento version](https://img.shields.io/badge/Magento-2.4.6%20%7C%202.4.9-orange)
![PHP version](https://img.shields.io/badge/PHP-8.2%E2%80%938.5-777BB4)
![License](https://img.shields.io/badge/License-OSL--3.0-blue)
![Latest Version](https://img.shields.io/packagist/v/yireo/magento2-events-logger)
<!-- badges.specs.end -->


> This Magento 2 module logs a triggered Magento event to the logs (for debugging purpose)

### Installation
Install this package via composer:
```bash
composer require yireo/magento2-events-logger
```

Next, enable this module:
```bash
bin/magento module:enable Yireo_EventsLogger
```

### Configuration
Logging is disabled by default. Enable it in the Admin Panel under **Stores > Configuration > Advanced > Yireo Events Logger**, or via the CLI:
```bash
bin/magento config:set yireo_events_logger/general/enabled 1
```

Every dispatched event is then written to `var/log/events.log`.

### Security notice
This module is meant for debugging only:
- A single page request dispatches hundreds of events, so `var/log/events.log` grows quickly. The file is not rotated by this module and can fill up the disk.
- The log reveals the internal flow of the shop (logins, orders, admin actions).

Do not leave logging enabled in production.

## Current status

<!-- badges.test.start -->
![Static Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_EventsLogger/static-tests.yml?label=static-tests)
![Unit Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_EventsLogger/unit-tests.yml?label=unit-tests)
![Integration Tests](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_EventsLogger/integration-tests.yml?label=integration-tests)
![Playwright](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_EventsLogger/playwright.yml?label=playwright)
![DI Compilation](https://img.shields.io/github/actions/workflow/status/yireo/Yireo_EventsLogger/compile.yml?label=compile)
<!-- badges.test.end -->

