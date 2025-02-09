<?php

namespace WebSK\Auth\RequestHandlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use WebSK\Slim\RequestHandlers\BaseHandler;

/**
 * Class GateHandler
 * @package WebSK\Auth\RequestHandlers
 */
class GateHandler extends BaseHandler
{
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        \Hybrid_Endpoint::process();
    }
}
