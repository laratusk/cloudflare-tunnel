# Changelog

All notable changes to this package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.2.0] - 2026-04-02

### Changed

- Tunnel lifecycle hooks are now handled exclusively through the `TunnelConnected` and `TunnelDisconnected` events; side effects such as webhook registration and removal belong in ordinary Laravel listeners
- README rewritten around the event-based workflow, with listener examples and the payload of each event

### Removed

- `after_connected` and `before_disconnected` callbacks from `config/cloudflare-tunnel.php`
- `afterConnected` and `beforeDisconnected` properties from the `TunnelConfig` DTO
- Callback execution and error reporting from the `cloudflare:tunnel` command

## [0.1.0] - 2026-04-02

### Added

- `cloudflare:tunnel` Artisan command to start and manage Cloudflare Tunnels
- Support for **named tunnels** with static hostnames
- Support for **quick tunnels** with randomly generated URLs
- Configurable `after_connected` callback for post-connection actions (e.g. webhook registration)
- Configurable `before_disconnected` callback for cleanup actions (e.g. webhook removal)
- Graceful shutdown with `SIGINT` / `SIGTERM` signal handling
- `TunnelConnected` and `TunnelDisconnected` events
- `CloudflareTunnel` facade resolving through `TunnelServiceInterface`
- Full configuration via `config/cloudflare-tunnel.php` and `.env`
- Validation of named-tunnel configuration, raising `InvalidConfigurationException` when `tunnel_name` or `hostname` is missing
- `CloudflaredResolverInterface` and `ProcessManagerInterface` contracts, with `TunnelService` depending on the interfaces rather than concrete helpers
- Support for Laravel 10, 11, 12 and 13 on PHP 8.2–8.4, covered by the CI matrix
- PHPStan level 9, Pint, Rector, and Pest test suite

[Unreleased]: https://github.com/laratusk/cloudflare-tunnel/compare/v0.2.0...HEAD
[0.2.0]: https://github.com/laratusk/cloudflare-tunnel/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/laratusk/cloudflare-tunnel/releases/tag/v0.1.0
