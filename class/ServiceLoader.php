<?php

namespace App;

use Authwave\Authenticator;
use Authwave\RedirectHandler;
use Gt\Http\Response;
use Gt\Http\ServerInfo;
use Gt\Session\Session;
use Gt\WebEngine\Middleware\DefaultServiceLoader;
use Psr\Http\Message\UriInterface;

class ServiceLoader extends DefaultServiceLoader {

    #[LazyLoad]
    public function loadAuthenticator(
        Session $session,
        Response $response,
        ServerInfo $serverInfo
    ):Authenticator {

        // die("loading authenticator");
        // create authwave session
        $authwaveSession = $session->getStore("AUTHWAVE", true);

        // create callback for redirect
        $redirectCallback = fn(string $newUri, int $code) => $response->redirect($newUri, $code);
        $redirecthandler = new class($redirectCallback) extends RedirectHandler {
            private $redirectCallback;
            public function __construct($redirectCallback) {
                $this->redirectCallback = $redirectCallback;
            }
            public function redirect(UriInterface $uri, int $code = 303) {
                call_user_func($this->redirectCallback, $uri, $code);
            }
        };

        return new Authenticator($this->config->getString("authwave.api_key"), $serverInfo->getRequestUri(), $this->config->getString("authwave.host"), $authwaveSession, $redirecthandler);
        
    }
}