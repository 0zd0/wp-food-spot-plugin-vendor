<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Symfony\Component\Cache\Marshaller;

use Onepix\FoodSpotVendor\Symfony\Component\Cache\Exception\CacheException;

/**
 * Serializes/unserializes values using igbinary_serialize() if available, serialize() otherwise.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DefaultMarshaller implements MarshallerInterface
{
    private bool $useIgbinarySerialize = false;
    private bool $throwOnSerializationFailure = false;

    public function __construct(?bool $useIgbinarySerialize = null, bool $throwOnSerializationFailure = false)
    {
        if ($useIgbinarySerialize && (!\extension_loaded('igbinary') || version_compare('3.1.6', phpversion('igbinary'), '>'))) {
            throw new CacheException(\extension_loaded('igbinary') ? 'Please upgrade the "igbinary" PHP extension to v3.1.6 or higher.' : 'The "igbinary" PHP extension is not loaded.');
        }
        $this->useIgbinarySerialize = true === $useIgbinarySerialize;
        $this->throwOnSerializationFailure = $throwOnSerializationFailure;
    }

    public function marshall(array $values, ?array &$failed): array
    {
        $serialized = $failed = [];

        foreach ($values as $id => $value) {
            try {
                if ($this->useIgbinarySerialize) {
                    $serialized[$id] = igbinary_serialize($value);
                } else {
                    $serialized[$id] = serialize($value);
                }
            } catch (\Exception $e) {
                if ($this->throwOnSerializationFailure) {
                    throw new \ValueError($e->getMessage(), 0, $e);
                }
                $failed[] = $id;
            }
        }

        return $serialized;
    }

    public function unmarshall(string $value): mixed
    {
        if ('b:0;' === $value) {
            return false;
        }
        if ('N;' === $value) {
            return null;
        }
        static $igbinaryNull;
        if ($value === $igbinaryNull ??= \extension_loaded('igbinary') ? igbinary_serialize(null) : false) {
            return null;
        }
        $unserializeCallbackHandler = ini_set('unserialize_callback_func', __CLASS__.'::handleUnserializeCallback');
        try {
            if (':' === ($value[1] ?? ':')) {
                if (false !== $value = unserialize($value)) {
                    return $value;
                }
            } elseif (false === $igbinaryNull) {
                throw new \RuntimeException('Failed to unserialize values, did you forget to install the "igbinary" extension?');
            } elseif (null !== $value = igbinary_unserialize($value)) {
                return $value;
            }

            throw new \DomainException(error_get_last() ? error_get_last()['message'] : 'Failed to unserialize values.');
        } catch (\Error $e) {
            throw new \ErrorException($e->getMessage(), $e->getCode(), \E_ERROR, $e->getFile(), $e->getLine());
        } finally {
            ini_set('unserialize_callback_func', $unserializeCallbackHandler);
        }
    }

    /**
     * @internal
     */
    public static function handleUnserializeCallback(string $class): never
    {
        throw new \DomainException('Class not found: '.$class);
    }
}
