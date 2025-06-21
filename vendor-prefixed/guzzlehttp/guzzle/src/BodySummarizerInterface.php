<?php

namespace Onepix\FoodSpotVendor\GuzzleHttp;

use Onepix\FoodSpotVendor\Psr\Http\Message\MessageInterface;

interface BodySummarizerInterface
{
    /**
     * Returns a summarized message body.
     */
    public function summarize(MessageInterface $message): ?string;
}
