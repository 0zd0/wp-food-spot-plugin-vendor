<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Support;

interface MessageProvider
{
    /**
     * Get the messages for the instance.
     *
     * @return \Onepix\FoodSpotVendor\Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag();
}
