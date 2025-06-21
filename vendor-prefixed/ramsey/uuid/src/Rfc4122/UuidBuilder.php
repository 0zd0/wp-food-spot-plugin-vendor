<?php

/**
 * This file is part of the ramsey/uuid library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) Ben Ramsey <ben@benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */

declare(strict_types=1);

namespace Onepix\FoodSpotVendor\Ramsey\Uuid\Rfc4122;

use Onepix\FoodSpotVendor\Ramsey\Uuid\Builder\UuidBuilderInterface;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Codec\CodecInterface;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Converter\NumberConverterInterface;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Converter\Time\UnixTimeConverter;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Converter\TimeConverterInterface;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Exception\UnsupportedOperationException;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Math\BrickMathCalculator;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Rfc4122\UuidInterface as Rfc4122UuidInterface;
use Onepix\FoodSpotVendor\Ramsey\Uuid\Uuid;
use Onepix\FoodSpotVendor\Ramsey\Uuid\UuidInterface;
use Throwable;

/**
 * UuidBuilder builds instances of RFC 9562 (formerly 4122) UUIDs
 *
 * @immutable
 */
class UuidBuilder implements UuidBuilderInterface
{
    private TimeConverterInterface $unixTimeConverter;

    /**
     * Constructs the DefaultUuidBuilder
     *
     * @param NumberConverterInterface $numberConverter The number converter to use when constructing the Uuid
     * @param TimeConverterInterface $timeConverter The time converter to use for converting Gregorian time extracted
     *     from version 1, 2, and 6 UUIDs to Unix timestamps
     * @param TimeConverterInterface | null $unixTimeConverter The time converter to use for converter Unix Epoch time
     *     extracted from version 7 UUIDs to Unix timestamps
     */
    public function __construct(
        private NumberConverterInterface $numberConverter,
        private TimeConverterInterface $timeConverter,
        ?TimeConverterInterface $unixTimeConverter = null,
    ) {
        $this->unixTimeConverter = $unixTimeConverter ?? new UnixTimeConverter(new BrickMathCalculator());
    }

    /**
     * Builds and returns a Uuid
     *
     * @param CodecInterface $codec The codec to use for building this Uuid instance
     * @param string $bytes The byte string from which to construct a UUID
     *
     * @return Rfc4122UuidInterface UuidBuilder returns instances of Rfc4122UuidInterface
     */
    public function build(CodecInterface $codec, string $bytes): UuidInterface
    {
        try {
            /** @var Fields $fields */
            $fields = $this->buildFields($bytes);

            if ($fields->isNil()) {
                return new NilUuid($fields, $this->numberConverter, $codec, $this->timeConverter);
            }

            if ($fields->isMax()) {
                return new MaxUuid($fields, $this->numberConverter, $codec, $this->timeConverter);
            }

            return match ($fields->getVersion()) {
                Uuid::UUID_TYPE_TIME => new UuidV1($fields, $this->numberConverter, $codec, $this->timeConverter),
                Uuid::UUID_TYPE_DCE_SECURITY
                    => new UuidV2($fields, $this->numberConverter, $codec, $this->timeConverter),
                Uuid::UUID_TYPE_HASH_MD5 => new UuidV3($fields, $this->numberConverter, $codec, $this->timeConverter),
                Uuid::UUID_TYPE_RANDOM => new UuidV4($fields, $this->numberConverter, $codec, $this->timeConverter),
                Uuid::UUID_TYPE_HASH_SHA1 => new UuidV5($fields, $this->numberConverter, $codec, $this->timeConverter),
                Uuid::UUID_TYPE_REORDERED_TIME
                    => new UuidV6($fields, $this->numberConverter, $codec, $this->timeConverter),
                Uuid::UUID_TYPE_UNIX_TIME
                    => new UuidV7($fields, $this->numberConverter, $codec, $this->unixTimeConverter),
                Uuid::UUID_TYPE_CUSTOM => new UuidV8($fields, $this->numberConverter, $codec, $this->timeConverter),
                default => throw new UnsupportedOperationException(
                    'The UUID version in the given fields is not supported by this UUID builder',
                ),
            };
        } catch (Throwable $e) {
            throw new UnableToBuildUuidException($e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * Proxy method to allow injecting a mock for testing
     */
    protected function buildFields(string $bytes): FieldsInterface
    {
        return new Fields($bytes);
    }
}
