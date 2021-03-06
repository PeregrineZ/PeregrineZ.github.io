<?php

namespace Plancke\HypixelPHP\responses;

use Plancke\HypixelPHP\cache\CacheTimes;
use Plancke\HypixelPHP\classes\HypixelObject;

class WatchdogStats extends HypixelObject {
    /**
     * @return int
     */
    public function getLastMinute() {
        return $this->getInt('watchdog_lastMinute');
    }

    /**
     * @return int
     */
    public function getTotal() {
        return $this->getInt('watchdog_lastMinute');
    }

    /**
     * @return int
     */
    public function getRollingDaily() {
        return $this->getInt('watchdog_rollingDaily');
    }

    function getCacheTimeKey() {
        return CacheTimes::WATCHDOG;
    }
}