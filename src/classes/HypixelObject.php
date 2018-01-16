<?php

namespace Plancke\HypixelPHP\classes;

use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\Response;
use Plancke\HypixelPHP\HypixelPHP;
use Plancke\HypixelPHP\util\CacheUtil;
use Plancke\HypixelPHP\util\Utilities;

/**
 * Class HypixelObject
 * @package Plancke\HypixelPHP\classes
 */
abstract class HypixelObject extends APIObject {

    protected $response;

    /**
     * @param            $data
     * @param HypixelPHP $HypixelPHP
     */
    public function __construct(HypixelPHP $HypixelPHP, $data) {
        parent::__construct($HypixelPHP, $data);

        if (!isset($this->data['record']) || !is_array($this->data['record'])) {
            $this->data['record'] = [];
        }
        if (!isset($this->data['extra']) || !is_array($this->data['extra'])) {
            $this->data['extra'] = [];
        }
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data['record'];
    }

    /**
     * Called when an object is fetched freshly
     * @param HypixelObject|null $cached The previous document, null or expired cached
     */
    public function handleNew($cached = null) {
        $this->data['timestamp'] = time();
    }

    /**
     * @param int $leeway
     * @return bool
     */
    public function isCached($leeway = 1) {
        return abs(time() - $this->getCachedTime()) > $leeway;
    }

    /**
     * @return int
     */
    public function getCachedTime() {
        return $this->data['timestamp'];
    }

    /**
     * @param int $extra extra time to be added to the check
     * @return bool
     */
    public function isCacheExpired($extra = 0) {
        return CacheUtil::isExpired($this->getCachedTime() * 1000, $this->getHypixelPHP()->getCacheHandler()->getCacheTime($this->getCacheTimeKey()) * 1000, $extra);
    }

    /**
     * @return string
     */
    public abstract function getCacheTimeKey();

    /**
     * @param array $extra
     */
    public function _setExtra($extra) {
        $this->data['extra'] = $extra;
    }

    /**
     * @param $input
     * @param bool $save
     * @throws HypixelPHPException
     */
    public function setExtra($input, $save = true) {
        $anyChange = false;
        foreach ($input as $key => $val) {
            if (array_key_exists($key, $this->data['extra'])) {
                if ($val == null) {
                    unset($this->data['extra'][$key]);
                    $anyChange = true;
                    continue;
                } else if ($this->data['extra'][$key] == $val) {
                    continue;
                }
            }
            $this->getHypixelPHP()->getLogger()->log('Extra \'' . $key . '\' set to ' . json_encode($val));
            $this->data['extra'][$key] = $val;
            $anyChange = true;
        }
        if ($anyChange && $save) {
            $this->getHypixelPHP()->getCacheHandler()->_setCache($this);
        }
    }

    /**
     * @return string
     */
    public function getID() {
        return $this->get('_id');
    }

    /**
     * @param string $key
     * @param mixed $default
     * @param string $delimiter
     * @return mixed
     */
    public function getExtra($key = null, $default = null, $delimiter = '.') {
        if ($key != null) {
            return Utilities::getRecursiveValue($this->data['extra'], $key, $default, $delimiter);
        }
        return $this->data['extra'];
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function attachResponse(Response $response) {
        $this->response = $response;
        return $this;
    }

    /**
     * @return Response|null
     */
    public function getResponse() {
        return $this->response;
    }

}