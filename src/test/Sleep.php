<?php

namespace Zeus\Async\test;

use Zeus\Async\AsyncInterface;

/**
 *
 */
readonly class Sleep implements AsyncInterface
{
    public function __construct(private int $seconds)
    {
    }

    /**
     * @return string
     */#[\Override]
    public function run(): string
    {
        sleep($this->seconds); // for check whether the async is running
        return $this->seconds;
    }

    /**
     * @param string $value
     * @return void
     */#[\Override]
    public function success(string $value): void
    {
        echo $value . PHP_EOL;
    }

    /**
     * @param string $error
     * @return void
     */#[\Override]
    public function fail(string $error): void
    {
        echo $error . PHP_EOL;
    }
}
