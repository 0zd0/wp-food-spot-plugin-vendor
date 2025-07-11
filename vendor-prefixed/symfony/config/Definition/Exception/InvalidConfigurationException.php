<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onepix\FoodSpotVendor\Symfony\Component\Config\Definition\Exception;

/**
 * A very general exception which can be thrown whenever non of the more specific
 * exceptions is suitable.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class InvalidConfigurationException extends Exception
{
    private ?string $path = null;
    private bool $containsHints = false;

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Adds extra information that is suffixed to the original exception message.
     */
    public function addHint(string $hint): void
    {
        if (!$this->containsHints) {
            $this->message .= "\nHint: ".$hint;
            $this->containsHints = true;
        } else {
            $this->message .= ', '.$hint;
        }
    }
}
