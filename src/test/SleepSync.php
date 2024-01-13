<?php

namespace Zeus\Async\test;

use Zeus\Async\AsyncInterface;
use Zeus\Async\Mutex;

/**
 *
 */
readonly class SleepSync implements AsyncInterface
{

    /**
     * @param Mutex $mutex
     * @param int $seconds
     */
    public function __construct(private Mutex $mutex, private int $seconds)
    {
    }

    /**
     * @return string
     */
    #[\Override]
    public function run(): string
    {
        $this->mutex->lock();
        sleep($this->seconds);
        $this->mutex->unlock();
        return $this->seconds;
    }

    /**
     * @param string $value
     * @return void
     */
    #[\Override]
    public function success(string $value): void
    {
        echo $value . PHP_EOL;
    }

    /**
     * @param string $error
     * @return void
     */
    #[\Override]
    public function fail(string $error): void
    {
        echo $error . PHP_EOL;
    }
}