<?php

namespace AgusSuroyo\Async;

use AgusSuroyo\Async\Contracts\ProcessManager;
use AgusSuroyo\Async\Utils\SystemInfo;

class Async implements ProcessManager
{
    protected array $ids = [];
    protected int $maxProcesses;

    public function __construct(?int $maxProcesses = null)
    {
        $this->maxProcesses = $maxProcesses ?? (SystemInfo::getCpuCores() * 2);
    }

    public function max(int $maxProcesses): self
    {
        if ($maxProcesses < 1) {
            throw new \InvalidArgumentException("Maximum processes must be at least 1");
        }
        $this->maxProcesses = $maxProcesses;
        return $this;
    }

    public function run(callable $callback): void
    {
        while (count($this->ids) >= $this->maxProcesses) {
            $this->waitForChild();
        }

        $pid = pcntl_fork();

        if ($pid == -1) {
            throw new \Exception("Failed to fork process");
        }

        if ($pid == 0) {
            call_user_func($callback);
            exit;
        }

        $this->ids[$pid] = true;
    }

    public function wait(): void
    {
        while (!empty($this->ids)) {
            $this->waitForChild();
        }
    }

    protected function waitForChild(): void
    {
        while (($pid = pcntl_waitpid(-1, $status, WNOHANG)) > 0) {
            unset($this->ids[$pid]); // Remove completed process
        }
        usleep(5000); // Prevents CPU overuse
    }
}