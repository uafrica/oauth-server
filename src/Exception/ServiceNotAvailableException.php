<?php

namespace OAuthServer\Exception;

use Cake\Http\Exception\HttpException;

/**
 * Thrown when the service is not available
 */
class ServiceNotAvailableException extends HttpException
{
    /**
     * @inheritDoc
     */
    protected $_defaultCode = 503;

    /**
     * @inheritDoc
     */
    public function __construct($message = null, $code = null, $previous = null)
    {
        if (empty($message)) {
            $message = 'Service unavailable';
        }
        parent::__construct($message, $code, $previous);
    }
}