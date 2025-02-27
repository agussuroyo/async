<?php

namespace AgusSuroyo\Async\Utils;

class SystemInfo
{
    public static function getCpuCores(): int
    {
        if (strncasecmp(PHP_OS, 'WIN', 3) === 0) {
            $cpuCores = (int) trim(shell_exec("wmic cpu get NumberOfLogicalProcessors /value | findstr \"[0-9]\""));
        } else {
            $cpuCores = (int) trim(shell_exec('nproc 2>/dev/null') ?: '1');
        }
        return max(1, $cpuCores);
    }
}