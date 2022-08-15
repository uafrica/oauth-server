<?php

namespace App\Error;

use Cake\Error\ExceptionRenderer as CakeExceptionRenderer;
use Cake\Http\Response;
use Exception;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * @inheritDoc
 */
class ExceptionRenderer extends CakeExceptionRenderer
{
    /**
     * @inheritDoc
     */
    protected function _code(Exception $exception)
    {
        $code = parent::_code($exception);
        if ($exception instanceof OAuthServerException) {
            $code = $exception->getHttpStatusCode();
        }
        return $code;
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        //fwrite(STDERR, \Cake\Error\Debugger::exportVar($this->error) . PHP_EOL);
        $exception = $this->error;
        $code      = $this->_code($exception);
        return new Response(['body' => $code, 'status' => $code]);
    }
}