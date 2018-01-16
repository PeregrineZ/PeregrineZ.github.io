<?php

namespace Plancke\HypixelPHP\responses\guild;

use Plancke\HypixelPHP\classes\APIHolding;
use Plancke\HypixelPHP\exceptions\HypixelPHPException;
use Plancke\HypixelPHP\fetch\FetchParams;
use Plancke\HypixelPHP\HypixelPHP;
use Plancke\HypixelPHP\responses\player\Player;

/**
 * Class GuildMember
 * @package Plancke\HypixelPHP\responses\guild
 */
class GuildMember extends APIHolding {
    protected $coinHistory;
    protected $uuid, $name;
    protected $joined;

    /**
     * @param HypixelPHP $HypixelPHP
     * @param $member
     */
    public function __construct(HypixelPHP $HypixelPHP, $member) {
        parent::__construct($HypixelPHP);


        if (isset($member['coinHistory'])) {
            $this->coinHistory = $member['coinHistory'];
        }
        if (isset($member['uuid'])) {
            $this->uuid = $member['uuid'];
        }
        if (isset($member['name'])) {
            $this->name = $member['name'];
        }
        if (isset($member['joined'])) {
            $this->joined = $member['joined'];
        }
    }

    /**
     * @return Player
     * @throws HypixelPHPException
     */
    public function getPlayer() {
        if (isset($this->uuid)) {
            return $this->getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_UUID => $this->uuid]);
        } else if (isset($this->name)) {
            return $this->getHypixelPHP()->getPlayer([FetchParams::PLAYER_BY_NAME => $this->name]);
        }
        return null;
    }

    /**
     * @return string
     */
    public function getUUID() {
        return $this->uuid;
    }

    /**
     * @return array
     */
    public function getCoinHistory() {
        return $this->coinHistory;
    }

    /**
     * @return int
     */
    public function getJoinTimeStamp() {
        return $this->joined;
    }
}