<?php

use League\OAuth2\Server\Exception\OAuthServerException;

if ($response instanceof OAuthServerException) {
    echo json_encode([
        'error' => $response->getErrorType(),
        'message' => $response->getMessage()
    ]);
} else {
    echo json_encode($response);
}
