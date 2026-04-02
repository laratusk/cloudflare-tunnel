<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Exceptions;

final class TunnelConnectionException extends CloudflareTunnelException
{
    public function __construct(string $message = 'Failed to establish a tunnel connection within the configured timeout.')
    {
        parent::__construct($message);
    }
}
