<?php

namespace Zeus\Async;

/**
 *
 */
class AsyncProcess
{
    /**
     * @var AsyncInterface[] $asyncObjects
     */
    private array $asyncObjects;


    /**
     * @var array
     */
    private array $progressing = [];


    /**
     * @var int
     */
    private readonly int $startTime;

    /**
     * @param int $timeout
     */
    public function __construct(private readonly int $timeout = 5)
    {
        $this->startTime = microtime(true);
    }

    /**
     * @param AsyncInterface $async
     * @return $this
     */
    public function add(AsyncInterface $async): self
    {
        $this->asyncObjects[] = $async;
        return $this;
    }

    /**
     * @return void
     */
    public function start(): void
    {
        foreach ($this->asyncObjects as $asyncObject) {
            $this->progressing[] = [
                'process' => Process::create(PhpCode::create($asyncObject)),
                'async_object' => $asyncObject
            ];
        }
    }

    /**
     * @return void
     */
    public function wait(): void
    {
        while (!empty($this->progressing)) {
            $this->processWithTimeout();
            usleep(1000);
        }
    }

    /**
     * @return void
     */
    private function processWithTimeout(): void
    {
        /**
         * @var Process $process
         * @var AsyncInterface $async
         */
        foreach ($this->progressing as $key => ['process' => $process, 'async_object' => $async]) {

            if ($this->isTimeExpired()) {
                proc_terminate($process->get(), 9);
                $async->fail('time has expired');
                unset($this->progressing[$key]);
            }

            if ($process->isRunning()) {
                continue;
            }
            
            if ($process->isFailed()) {
                $async->fail($process->getError());
            } else {
                $async->success($process->getOutput());
            }

            unset($this->progressing[$key]);
            $process->close();

        }
    }

    /**
     * @return bool
     */
    private function isTimeExpired(): bool
    {
        $differenceTime = time() - $this->startTime;
        return ($differenceTime) > (float)$this->timeout;
    }
}
