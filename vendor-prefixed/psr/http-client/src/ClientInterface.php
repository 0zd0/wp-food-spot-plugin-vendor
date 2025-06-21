<?php

namespace Onepix\FoodSpotVendor\Psr\Http\Client;

use Onepix\FoodSpotVendor\Psr\Http\Message\RequestInterface;
use Onepix\FoodSpotVendor\Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     *
     * @throws \Onepix\FoodSpotVendor\Psr\Http\Client\ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest(RequestInterface $request): ResponseInterface;
}
