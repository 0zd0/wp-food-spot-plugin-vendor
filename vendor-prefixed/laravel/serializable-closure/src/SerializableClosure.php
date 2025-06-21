<?php

namespace Onepix\FoodSpotVendor\Laravel\SerializableClosure;

use Closure;
use Onepix\FoodSpotVendor\Laravel\SerializableClosure\Exceptions\InvalidSignatureException;
use Onepix\FoodSpotVendor\Laravel\SerializableClosure\Serializers\Signed;
use Onepix\FoodSpotVendor\Laravel\SerializableClosure\Signers\Hmac;
class SerializableClosure
{
    /**
     * The closure's serializable.
     *
     * @var \Onepix\FoodSpotVendor\Laravel\SerializableClosure\Contracts\Serializable
     */
    protected $serializable;
    /**
     * Creates a new serializable closure instance.
     *
     * @param  \Closure  $closure
     * @return void
     */
    public function __construct(Closure $closure)
    {
        $this->serializable = \Onepix\FoodSpotVendor\Laravel\SerializableClosure\Serializers\Signed::$signer ? new Serializers\Signed($closure) : new Serializers\Native($closure);
    }
    /**
     * Resolve the closure with the given arguments.
     *
     * @return mixed
     */
    public function __invoke()
    {
        return call_user_func_array($this->serializable, func_get_args());
    }
    /**
     * Gets the closure.
     *
     * @return \Closure
     */
    public function getClosure()
    {
        return $this->serializable->getClosure();
    }
    /**
     * Create a new unsigned serializable closure instance.
     *
     * @param  Closure  $closure
     * @return \Onepix\FoodSpotVendor\Laravel\SerializableClosure\UnsignedSerializableClosure
     */
    public static function unsigned(Closure $closure)
    {
        return new UnsignedSerializableClosure($closure);
    }
    /**
     * Sets the serializable closure secret key.
     *
     * @param  string|null  $secret
     * @return void
     */
    public static function setSecretKey($secret)
    {
        \Onepix\FoodSpotVendor\Laravel\SerializableClosure\Serializers\Signed::$signer = $secret ? new Hmac($secret) : null;
    }
    /**
     * Sets the serializable closure secret key.
     *
     * @param  \Closure|null  $transformer
     * @return void
     */
    public static function transformUseVariablesUsing($transformer)
    {
        \Onepix\FoodSpotVendor\Laravel\SerializableClosure\Serializers\Native::$transformUseVariables = $transformer;
    }
    /**
     * Sets the serializable closure secret key.
     *
     * @param  \Closure|null  $resolver
     * @return void
     */
    public static function resolveUseVariablesUsing($resolver)
    {
        \Onepix\FoodSpotVendor\Laravel\SerializableClosure\Serializers\Native::$resolveUseVariables = $resolver;
    }
    /**
     * Get the serializable representation of the closure.
     *
     * @return array{serializable: \Onepix\FoodSpotVendor\Laravel\SerializableClosure\Serializers\Signed|\Onepix\FoodSpotVendor\Laravel\SerializableClosure\Contracts\Serializable}
     */
    public function __serialize()
    {
        return ['serializable' => $this->serializable];
    }
    /**
     * Restore the closure after serialization.
     *
     * @param  array{serializable: \Onepix\FoodSpotVendor\Laravel\SerializableClosure\Serializers\Signed|\Onepix\FoodSpotVendor\Laravel\SerializableClosure\Contracts\Serializable}  $data
     * @return void
     *
     * @throws \Onepix\FoodSpotVendor\Laravel\SerializableClosure\Exceptions\InvalidSignatureException
     */
    public function __unserialize($data)
    {
        if (Signed::$signer && !$data['serializable'] instanceof Signed) {
            throw new InvalidSignatureException();
        }
        $this->serializable = $data['serializable'];
    }
}