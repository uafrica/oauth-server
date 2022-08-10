<?php

use League\OAuth2\Server\RequestTypes\AuthorizationRequest;

/* @var AuthorizationRequest $authRequest */
?>
<h1><?= $authRequest->getClient()->getName() ?> would like to access:</h1>