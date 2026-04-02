<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Contracts;

interface ProcessManagerInterface
{
    /**
     * Open a new process with the given command.
     *
     * @param  list<string>  $command
     */
    public function open(array $command): bool;

    /**
     * Read a line from stderr (non-blocking).
     */
    public function readStderr(): ?string;

    /**
     * Check whether the process is still running.
     */
    public function isRunning(): bool;

    /**
     * Terminate the process and close all pipes.
     */
    public function terminate(): void;
}
