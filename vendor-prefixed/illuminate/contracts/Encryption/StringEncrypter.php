<?php

namespace Onepix\FoodSpotVendor\Illuminate\Contracts\Encryption;

interface StringEncrypter
{
    /**
     * Encrypt a string without serialization.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \Onepix\FoodSpotVendor\Illuminate\Contracts\Encryption\EncryptException
     */
    public function encryptString(#[\SensitiveParameter] $value);

    /**
     * Decrypt the given string without unserialization.
     *
     * @param  string  $payload
     * @return string
     *
     * @throws \Onepix\FoodSpotVendor\Illuminate\Contracts\Encryption\DecryptException
     */
    public function decryptString($payload);
}
