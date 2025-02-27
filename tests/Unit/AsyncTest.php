<?php

class AsyncTest extends \PHPUnit\Framework\TestCase
{
    public function testRunInMax3Second()
    {
        $async = new \AgusSuroyo\Async\Async();

        $start = time();

        $sets = [1, 2, 3];
        foreach ($sets as $set) {
            $async->run(function () use ($set) {
                sleep($set);
            });
        }

        $async->wait();

        $stop = time();
        $this->assertGreaterThanOrEqual(3, $stop - $start);
    }

    public function testAbleToPutFileWithoutWaiting()
    {
        $async = new \AgusSuroyo\Async\Async();

        $sets = array_map(function ($set) {
            return 'storages/' . $set . '.txt';
        }, range(1, 3));

        foreach ($sets as $set) {
            if (file_exists($set)) {
                unlink($set);
            }
        }

        foreach ($sets as $set) {
            $async->run(function () use ($set) {
                $this->put($set);
            });
        }

        // Just manual sleep for buffer before assert
        sleep(2);

        foreach ($sets as $set) {
            $this->assertFileExists($set);
        }
    }

    public function testExceedingMaxProcessesLimit()
    {
        $async = new \AgusSuroyo\Async\Async(2);

        $start = time();

        for ($i = 0; $i < 4; $i++) {
            $async->run(function () {
                sleep(2);
            });
        }

        $async->wait();
        $stop = time();

        $this->assertGreaterThanOrEqual(4, $stop - $start, "Processes should be queued when exceeding the limit");
    }

    public function testEmptyCallback()
    {
        $async = new \AgusSuroyo\Async\Async();

        $this->expectNotToPerformAssertions();

        $async->run(function () {
            // No operation
        });

        $async->wait();
    }

    public function testInvalidCallable()
    {
        $async = new \AgusSuroyo\Async\Async();

        $this->expectException(\TypeError::class);

        $async->run("invalid_function");
    }

    public function testProcessCleanup()
    {
        $async = new \AgusSuroyo\Async\Async();

        for ($i = 0; $i < 3; $i++) {
            $async->run(function () {
                sleep(1);
            });
        }

        $async->wait();

        $reflection = new \ReflectionClass($async);
        $idsProperty = $reflection->getProperty('ids');
        $idsProperty->setAccessible(true);

        $this->assertEmpty($idsProperty->getValue($async), "Process list should be empty after all processes finish");
    }

    private function put($filename = '0', $string = ''): bool
    {
        if (!file_exists($filename)) {
            touch($filename);
        }
        return file_put_contents($filename, $string);
    }
}
