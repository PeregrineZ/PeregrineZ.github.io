<?php

namespace Plancke\HypixelPHP\exceptions;

class InvalidUUIDException extends HypixelPHPException {

    private $val;

    public function __construct($val) {
        parent::__construct("'$val' isn't a valid UUID", ExceptionCodes::INVALID_UUID);

        $this->val = $val;
    }

    /**
     * @return string
     */
    public function getVal(): string {
        return $this->val;
    }

}