<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Tunnel Mode
    |--------------------------------------------------------------------------
    |
    | "named"  — Uses a pre-configured tunnel with a static hostname.
    |            Requires `tunnel_name` and `hostname` to be set.
    |
    | "quick"  — Starts a temporary tunnel with a random *.trycloudflare.com URL.
    |            No Cloudflare account or configuration required.
    |
    */

    'mode' => env('CLOUDFLARE_TUNNEL_MODE', 'quick'),

    /*
    |--------------------------------------------------------------------------
    | Named Tunnel Configuration
    |--------------------------------------------------------------------------
    |
    | These values are only required when `mode` is set to "named".
    |
    | `tunnel_name` — The name of the tunnel as shown in `cloudflared tunnel list`.
    | `hostname`    — The DNS hostname routed to this tunnel (e.g. tunnel.example.com).
    |
    */

    'tunnel_name' => env('CLOUDFLARE_TUNNEL_NAME'),

    'hostname' => env('CLOUDFLARE_TUNNEL_HOSTNAME'),

    /*
    |--------------------------------------------------------------------------
    | Local Server
    |--------------------------------------------------------------------------
    |
    | The local URL that the tunnel will forward traffic to, and an optional
    | Host header override (useful when your local server uses virtual hosts).
    |
    */

    'local_url' => env('CLOUDFLARE_TUNNEL_LOCAL_URL', 'http://127.0.0.1'),

    'host_header' => env('CLOUDFLARE_TUNNEL_HOST_HEADER'),

    /*
    |--------------------------------------------------------------------------
    | Connection Timeout
    |--------------------------------------------------------------------------
    |
    | Maximum number of seconds to wait for the tunnel to establish a
    | connection before giving up.
    |
    */

    'timeout' => (int) env('CLOUDFLARE_TUNNEL_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Callbacks
    |--------------------------------------------------------------------------
    |
    | Optional callbacks that fire after the tunnel connects or before it
    | disconnects. Useful for registering/removing webhooks, sending
    | notifications, or any other side effects.
    |
    | Each value should be a callable or null. The `after_connected` callback
    | receives the tunnel URL as its only argument. The `before_disconnected`
    | callback receives the tunnel URL as well.
    |
    | Example:
    |
    |   'after_connected' => function (string $url) {
    |       Telegram::setWebhook(['url' => $url . '/webhook']);
    |   },
    |
    |   'before_disconnected' => function (string $url) {
    |       Telegram::deleteWebhook();
    |   },
    |
    */

    'after_connected' => null,

    'before_disconnected' => null,

];
