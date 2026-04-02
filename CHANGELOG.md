# Changelog

All notable changes to this package will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2026-04-02

### Added

- `cloudflare:tunnel` Artisan command to start and manage Cloudflare Tunnels
- Support for **named tunnels** with static hostnames
- Support for **quick tunnels** with randomly generated URLs
- Configurable `after_connected` callback for post-connection actions (e.g. webhook registration)
- Configurable `before_disconnected` callback for cleanup actions (e.g. webhook removal)
- Graceful shutdown with `SIGINT` / `SIGTERM` signal handling
- `TunnelConnected` and `TunnelDisconnected` events
- Full configuration via `config/cloudflare-tunnel.php` and `.env`
- PHPStan level 9, Pint, Rector, and Pest test suite
