<?php

use Authwave\Authenticator;

function go(Authenticator $authenticator) {
    var_dump($authenticator->isLoggedIn());
    die;
}