<?php

namespace App\Exceptions;
/**
 * Description of AppExceptions
 *
 * @author ayme.pignon
 */
class AppException extends Exception{
    public function __construct(string $message) {
        parent::__construct($message);
    }
}
