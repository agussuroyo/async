<?php

namespace AgusSuroyo\Async\Contracts;

interface ProcessManager
{
    public function run(callable $callback): void;
    public function wait(): void;
}