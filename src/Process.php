<?php

namespace Zeus\Async;

use App\exceptions\ProcessException;

/**
 *
 */
class Process
{
    /**
     * @var mixed
     */
    private readonly mixed $process;


    /**
     * @var mixed
     */
    private mixed $stdIn = null;

    /**
     * @var mixed
     */
    private mixed $stdOut = null;

    /**
     * @var mixed
     */
    private mixed $stdError = null;

    /**
     * @param $process
     */
    public function __construct($process)
    {
        $this->process = $process;
    }

    /***
     * @param string $phpCode
     * @return self
     */
    public static function create(string $phpCode): self
    {
        $resource = proc_open('php', [['pipe', 'r'], ['pipe', 'w'], ['pipe', 'w']], $pipes);
        if (is_resource($resource)) {
            foreach ($pipes as $pipe) {
                stream_set_blocking($pipe, 0);
            }
            return self::getInstance($resource, $pipes, $phpCode);
        }
        throw new ProcessException('Could not create a process');
    }

    /**
     * @param $resource
     * @param array $pipes
     * @param string $phpCode
     * @return self
     */
    private static function getInstance($resource, array $pipes, string $phpCode): static
    {
        $process = new self($resource);
        $process->setStdIn($pipes[0]);
        $process->setStdOut($pipes[1]);
        $process->setStdError($pipes[2]);
        fwrite($process->getStdIn(), $phpCode);
        fclose($process->getStdIn());
        return $process;
    }

    /**
     * @return mixed
     */
    public function get(): mixed
    {
        return $this->process;
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->getStatus()['running'];
    }

    /**
     * @return bool
     */
    public function isFailed(): bool
    {
        $status = $this->getStatus();
        if ($status['signaled'] || $status['stopped']) {
            return true;
        }

        return !$status['running'] && $status['exitcode'] !== 0;
    }

    /**
     * @return bool
     */
    public function isTerminated(): bool
    {
        return $this->getStatus()['stopped'];
    }


    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->getStatus()['pid'];
    }

    /**
     * @return array{
     *     'pid': integer,
     *     'running':boolean,
     *     'signaled':boolean,
     *     'stopped':boolean,
     *     'exitcode':integer,
     *     'termsig':integer,
     *     'stopsig':integer
     * }
     */
    public function getStatus(): array
    {
        return proc_get_status($this->process);
    }

    /**
     * @param mixed $stdIn
     */
    public function setStdIn(mixed $stdIn): void
    {
        $this->throwIfNotResource($stdIn, 'stdin have to a resource');
        $this->stdIn = $stdIn;
    }

    /**
     * @param mixed $stdOut
     */
    public function setStdOut(mixed $stdOut): void
    {
        $this->throwIfNotResource($stdOut, 'stdOut have to a resource');
        $this->stdOut = $stdOut;
    }

    /**
     * @param mixed $stdError
     * @return void
     */
    public function setStdError(mixed $stdError): void
    {
        $this->throwIfNotResource($stdError, 'stdError have to a resource');
        $this->stdError = $stdError;
    }

    /**
     * @param mixed $resource
     * @param string $message
     * @return void
     */
    private function throwIfNotResource(mixed $resource, string $message): void
    {
        if (!is_resource($resource)) {
            throw new ProcessException($message);
        }
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return stream_get_contents($this->stdError);
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return stream_get_contents($this->stdOut);
    }

    /**
     * @return mixed
     */
    public function getStdIn(): mixed
    {
        return $this->stdIn;
    }

    /**
     * @return mixed
     */
    public function getStdError(): mixed
    {
        return $this->stdError;
    }

    /**
     * @return mixed
     */
    public function getStdOut(): mixed
    {
        return $this->stdOut;
    }

    /**
     * @return void
     */
    public function close(): void
    {
        $resources = [
            $this->process,
            $this->stdIn,
            $this->stdOut,
            $this->stdError
        ];
        foreach ($resources as $resource) {
            if (is_resource($resource)) {
                proc_close($resource);
            }
        }
    }

    /**
     * @return bool
     */
    public function isResource(): bool
    {
        return is_resource($this->process);
    }
}
