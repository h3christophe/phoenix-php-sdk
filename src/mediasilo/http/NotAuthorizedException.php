<?php

namespace mediasilo\http;

use Exception;
use mediasilo\http\ratelimit\RateLimt;

class NotAuthorizedException extends Exception {

    public function __construct($message, $errors) {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->errors}]: {$this->message}\n";
    }

    public function getErrors() {
        return $this->errors;
    }

}