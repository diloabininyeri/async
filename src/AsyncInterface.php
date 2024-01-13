<?php

namespace Zeus\Async;

/**
 *
 */
interface AsyncInterface
{

    /**
     * @return mixed
     */
    public function run(): string;

    /**
     * @param string $value
     * @return void
     */
    public function success(string $value): void;


    /**
     * @param string $error
     * @return void
     */
    public function fail(string $error): void;
}

