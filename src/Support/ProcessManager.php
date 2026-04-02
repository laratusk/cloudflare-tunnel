<?php

declare(strict_types=1);

namespace Laratusk\CloudflareTunnel\Support;

final class ProcessManager
{
    /** @var resource|null */
    private mixed $process = null;

    /** @var array<int, resource> */
    private array $pipes = [];

    /**
     * Open a new process with the given command.
     *
     * @param  list<string>  $command
     */
    public function open(array $command): bool
    {
        $result = proc_open(
            $command,
            [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ],
            $this->pipes,
        );

        if (! is_resource($result)) {
            return false;
        }

        $this->process = $result;

        stream_set_blocking($this->pipes[2], false);

        return true;
    }

    /**
     * Read a line from stderr (non-blocking).
     */
    public function readStderr(): ?string
    {
        if (! isset($this->pipes[2]) || ! is_resource($this->pipes[2])) {
            return null;
        }

        $line = fgets($this->pipes[2]);

        return $line !== false ? $line : null;
    }

    /**
     * Check whether the process is still running.
     */
    public function isRunning(): bool
    {
        if ($this->process === null || ! is_resource($this->process)) {
            return false;
        }

        $status = proc_get_status($this->process);

        return $status['running'];
    }

    /**
     * Terminate the process and close all pipes.
     */
    public function terminate(): void
    {
        foreach ($this->pipes as $pipe) {
            if (is_resource($pipe)) {
                fclose($pipe);
            }
        }

        if ($this->process !== null && is_resource($this->process)) {
            proc_terminate($this->process);
        }

        $this->pipes = [];
        $this->process = null;
    }
}
