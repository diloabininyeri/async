<?php

namespace Zeus\Async;

use Serializable;
use SysvSemaphore;

/**
 *
 */
class Mutex
{

    /**
     * @var string
     */
    private string $key = __FILE__ . __CLASS__;

    /**
     * @var bool
     */
    private bool $isRelease = false;
    /**
     * @var bool
     */
    private bool $isAcquire = false;

    /**
     * @var false|SysvSemaphore
     */
    private false|SysvSemaphore $semaphore;

    private readonly string $semaphoreKey;

    /**
     *
     */
    public function __construct()
    {
        $this->semaphore = sem_get(
            $this->semaphoreKey = crc32($this->key)
        );
    }


    /**
     * @return bool
     */
    public function lock(): bool
    {
        if ($this->isAcquire === false && $this->isRelease === false) {
            $this->isAcquire = sem_acquire($this->semaphore);
        }
        return $this->isAcquire;

    }


    /**
     * @return bool
     */
    public function unlock(): bool
    {
        if ($this->isRelease === false && $this->isAcquire === true) {
            $this->isRelease = sem_release($this->semaphore);
        }
        return $this->isRelease;
    }

    public function __sleep()
    {
        return ['key', 'isRelease', 'isAcquire', 'semaphoreKey'];
    }

    public function __wakeup()
    {
        $this->semaphore = sem_get($this->semaphoreKey);
    }

    /**
     *
     */
    public function __destruct()
    {
        if ($this->isRelease === false) {
            $this->unlock();
        }
    }
}