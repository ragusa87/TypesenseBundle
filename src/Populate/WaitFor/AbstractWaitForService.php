<?php

namespace Biblioverse\TypesenseBundle\Populate\WaitFor;

abstract class AbstractWaitForService implements WaitForInterface
{
    public function wait(int $maxSteps, ?callable $callable = null, int $sleepInSeconds = 1): void
    {
        if ($maxSteps < 1) {
            return;
        }

        $step = 1;
        do {
            try {
                $this->doCheck();

                return;
            } catch (\Throwable $e) {
                $lastException = $e;
                if (is_callable($callable)) {
                    $callable($step, $maxSteps, $e);
                }
                sleep($sleepInSeconds);
            }
        } while ($step++ < $maxSteps);

        throw new \RuntimeException(sprintf('%s is not available', $this->getName()), 0, $lastException);
    }

    abstract public function getName(): string;

    /**
     * @throws \Exception
     */
    abstract public function doCheck(): void;
}
