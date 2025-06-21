<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Mail;

interface Factory
{
    /**
     * Get a mailer instance by name.
     *
     * @param  string|null  $name
     * @return \Onepix\FoodSpotVendor\Illuminate\Contracts\Mail\Mailer
     */
    public function mailer($name = null);
}
