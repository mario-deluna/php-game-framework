<?php  

namespace PGF\Common;

use PGF\Exception;

class FrameLimiter
{
    private $target;

    private $start;

    public function __construct(int $targetFPS = 60)
    {
        $this->target = 1 / $targetFPS;
    }

    public function start()
    {
        $this->start = microtime(true);
    }

    public function wait()
    {
        if (($wait = ($this->target - (microtime(true) - $this->start)) * 1000000) > 0) usleep($wait);
    }
}
