# Cloudflare Tunnel for Laravel

[![CI](https://github.com/laratusk/cloudflare-tunnel/actions/workflows/ci.yml/badge.svg)](https://github.com/laratusk/cloudflare-tunnel/actions/workflows/ci.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/laratusk/cloudflare-tunnel.svg)](https://packagist.org/packages/laratusk/cloudflare-tunnel)
[![License](https://img.shields.io/packagist/l/laratusk/cloudflare-tunnel.svg)](LICENSE.md)

Start and manage [Cloudflare Tunnels](https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/) directly from Artisan. Expose your local Laravel application to the internet with a single command — using either a **quick tunnel** (random URL, zero config) or a **named tunnel** (static hostname, permanent URL).

```bash
php artisan cloudflare:tunnel
```

## Features

- **Quick tunnels** — Random `*.trycloudflare.com` URL, no account required
- **Named tunnels** — Static hostname under your own domain (e.g. `tunnel.example.com`)
- **Lifecycle callbacks** — Run code after the tunnel connects or before it disconnects (e.g. register/remove a Telegram webhook)
- **Events** — `TunnelConnected` and `TunnelDisconnected` events dispatched automatically
- **Graceful shutdown** — Clean process termination on `Ctrl+C`

## Requirements

- PHP 8.2+
- Laravel 10, 11, 12, or 13
- The `pcntl` PHP extension
- The [`cloudflared`](https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/downloads/) CLI binary

## Installation

### 1. Install `cloudflared`

**macOS (Homebrew):**

```bash
brew install cloudflared
```

**Linux (Debian/Ubuntu):**

```bash
curl -fsSL https://pkg.cloudflare.com/cloudflare-main.gpg | sudo tee /usr/share/keyrings/cloudflare-main.gpg >/dev/null
echo "deb [signed-by=/usr/share/keyrings/cloudflare-main.gpg] https://pkg.cloudflare.com/cloudflared $(lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/cloudflared.list
sudo apt update && sudo apt install cloudflared
```

**Other platforms:** See the [official download page](https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/downloads/).

Verify the installation:

```bash
cloudflared --version
```

### 2. Install the package

```bash
composer require laratusk/cloudflare-tunnel
```

### 3. Publish the config (optional)

```bash
php artisan vendor:publish --tag=cloudflare-tunnel-config
```

## Quick Start

### Quick Tunnel (zero config)

Run the command with no additional setup — you get a random public URL instantly:

```bash
php artisan cloudflare:tunnel
```

```
 Tunnel URL: https://random-words.trycloudflare.com
 Tunnel is running. Press Ctrl+C to stop.
```

### Named Tunnel (static hostname)

A named tunnel gives you a **permanent, predictable URL** under your own domain. Follow these steps once to set it up:

#### Step 1: Authenticate with Cloudflare

```bash
cloudflared tunnel login
```

This opens your browser. Select the domain you want to use and authorize `cloudflared`.

#### Step 2: Create a tunnel

```bash
cloudflared tunnel create my-app
```

Note the tunnel ID in the output — you'll see it in `cloudflared tunnel list` too.

#### Step 3: Route DNS to the tunnel

```bash
cloudflared tunnel route dns my-app tunnel.example.com
```

This creates a CNAME record pointing `tunnel.example.com` to your tunnel.

#### Step 4: Create the cloudflared config file

Create `~/.cloudflared/config.yml`:

```yaml
tunnel: <TUNNEL-ID>
credentials-file: /path/to/.cloudflared/<TUNNEL-ID>.json

ingress:
  - hostname: tunnel.example.com
    service: http://127.0.0.1
    originRequest:
      httpHostHeader: my-app.test
  - service: http_status:404
```

> Replace `<TUNNEL-ID>` with your actual tunnel ID. The `httpHostHeader` should match your local development domain. The credentials file path is printed when you create the tunnel.

#### Step 5: Configure the package

Add to your `.env`:

```env
CLOUDFLARE_TUNNEL_MODE=named
CLOUDFLARE_TUNNEL_NAME=my-app
CLOUDFLARE_TUNNEL_HOSTNAME=tunnel.example.com
```

#### Step 6: Run it

```bash
php artisan cloudflare:tunnel
```

```
 Tunnel URL: https://tunnel.example.com
 Tunnel is running. Press Ctrl+C to stop.
```

## Configuration

| Environment Variable | Default | Description |
|---|---|---|
| `CLOUDFLARE_TUNNEL_MODE` | `quick` | `quick` or `named` |
| `CLOUDFLARE_TUNNEL_NAME` | — | Named tunnel name (required for `named` mode) |
| `CLOUDFLARE_TUNNEL_HOSTNAME` | — | Static hostname (required for `named` mode) |
| `CLOUDFLARE_TUNNEL_LOCAL_URL` | `http://127.0.0.1` | Local URL to forward traffic to |
| `CLOUDFLARE_TUNNEL_HOST_HEADER` | — | Override the Host header for local requests |
| `CLOUDFLARE_TUNNEL_TIMEOUT` | `30` | Seconds to wait for tunnel connection |

## Lifecycle Callbacks

Register callbacks in your published `config/cloudflare-tunnel.php` to run code when the tunnel connects or disconnects. This is useful for webhook registration:

```php
use Telegram\Bot\Laravel\Facades\Telegram;

return [
    // ...

    'after_connected' => function (string $url) {
        Telegram::setWebhook(['url' => $url . '/api/telegram/webhook']);
    },

    'before_disconnected' => function (string $url) {
        Telegram::deleteWebhook();
    },
];
```

## Events

The package dispatches two events that you can listen for:

| Event | Payload |
|---|---|
| `TunnelConnected` | `string $url`, `TunnelMode $mode` |
| `TunnelDisconnected` | `string $url` |

```php
use Laratusk\CloudflareTunnel\Events\TunnelConnected;

Event::listen(TunnelConnected::class, function (TunnelConnected $event) {
    Log::info("Tunnel connected: {$event->url}");
});
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
