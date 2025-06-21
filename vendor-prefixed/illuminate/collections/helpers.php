<?php

use Onepix\FoodSpotVendor\Illuminate\Support\Arr;
use Onepix\FoodSpotVendor\Illuminate\Support\Collection;

if (! function_exists('onepix_foodspotvendor_collect')) {
    /**
     * Create a collection from the given value.
     *
     * @template TKey of array-key
     * @template TValue
     *
     * @param  \Onepix\FoodSpotVendor\Illuminate\Contracts\Support\Arrayable<TKey, TValue>|iterable<TKey, TValue>|null  $value
     * @return \Onepix\FoodSpotVendor\Illuminate\Support\Collection<TKey, TValue>
     */
    function onepix_foodspotvendor_collect($value = [])
    {
        return new Collection($value);
    }
}

if (! function_exists('onepix_foodspotvendor_data_fill')) {
    /**
     * Fill in data where it's missing.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @return mixed
     */
    function onepix_foodspotvendor_data_fill(&$target, $key, $value)
    {
        return onepix_foodspotvendor_data_set($target, $key, $value, false);
    }
}

if (! function_exists('onepix_foodspotvendor_data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string|array|int|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    function onepix_foodspotvendor_data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', $key);

        foreach ($key as $i => $segment) {
            unset($key[$i]);

            if (is_null($segment)) {
                return $target;
            }

            if ($segment === '*') {
                if ($target instanceof Collection) {
                    $target = $target->all();
                } elseif (! is_iterable($target)) {
                    return onepix_foodspotvendor_value($default);
                }

                $result = [];

                foreach ($target as $item) {
                    $result[] = onepix_foodspotvendor_data_get($item, $key);
                }

                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }

            $segment = match ($segment) {
                '\*' => '*',
                '\{first}' => '{first}',
                '{first}' => array_key_first(is_array($target) ? $target : (new Collection($target))->all()),
                '\{last}' => '{last}',
                '{last}' => array_key_last(is_array($target) ? $target : (new Collection($target))->all()),
                default => $segment,
            };

            if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return onepix_foodspotvendor_value($default);
            }
        }

        return $target;
    }
}

if (! function_exists('onepix_foodspotvendor_data_set')) {
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param  mixed  $target
     * @param  string|array  $key
     * @param  mixed  $value
     * @param  bool  $overwrite
     * @return mixed
     */
    function onepix_foodspotvendor_data_set(&$target, $key, $value, $overwrite = true)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*') {
            if (! Arr::accessible($target)) {
                $target = [];
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    onepix_foodspotvendor_data_set($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (Arr::accessible($target)) {
            if ($segments) {
                if (! Arr::exists($target, $segment)) {
                    $target[$segment] = [];
                }

                onepix_foodspotvendor_data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || ! Arr::exists($target, $segment)) {
                $target[$segment] = $value;
            }
        } elseif (is_object($target)) {
            if ($segments) {
                if (! isset($target->{$segment})) {
                    $target->{$segment} = [];
                }

                onepix_foodspotvendor_data_set($target->{$segment}, $segments, $value, $overwrite);
            } elseif ($overwrite || ! isset($target->{$segment})) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];

            if ($segments) {
                onepix_foodspotvendor_data_set($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }
}

if (! function_exists('onepix_foodspotvendor_data_forget')) {
    /**
     * Remove / unset an item from an array or object using "dot" notation.
     *
     * @param  mixed  $target
     * @param  string|array|int|null  $key
     * @return mixed
     */
    function onepix_foodspotvendor_data_forget(&$target, $key)
    {
        $segments = is_array($key) ? $key : explode('.', $key);

        if (($segment = array_shift($segments)) === '*' && Arr::accessible($target)) {
            if ($segments) {
                foreach ($target as &$inner) {
                    onepix_foodspotvendor_data_forget($inner, $segments);
                }
            }
        } elseif (Arr::accessible($target)) {
            if ($segments && Arr::exists($target, $segment)) {
                onepix_foodspotvendor_data_forget($target[$segment], $segments);
            } else {
                Arr::forget($target, $segment);
            }
        } elseif (is_object($target)) {
            if ($segments && isset($target->{$segment})) {
                onepix_foodspotvendor_data_forget($target->{$segment}, $segments);
            } elseif (isset($target->{$segment})) {
                unset($target->{$segment});
            }
        }

        return $target;
    }
}

if (! function_exists('onepix_foodspotvendor_head')) {
    /**
     * Get the first element of an array. Useful for method chaining.
     *
     * @param  array  $array
     * @return mixed
     */
    function onepix_foodspotvendor_head($array)
    {
        return reset($array);
    }
}

if (! function_exists('onepix_foodspotvendor_last')) {
    /**
     * Get the last element from an array.
     *
     * @param  array  $array
     * @return mixed
     */
    function onepix_foodspotvendor_last($array)
    {
        return end($array);
    }
}

if (! function_exists('onepix_foodspotvendor_value')) {
    /**
     * Return the default value of the given value.
     *
     * @template TValue
     * @template TArgs
     *
     * @param  TValue|\Closure(TArgs): TValue  $value
     * @param  TArgs  ...$args
     * @return TValue
     */
    function onepix_foodspotvendor_value($value, ...$args)
    {
        return $value instanceof Closure ? $value(...$args) : $value;
    }
}

if (! function_exists('onepix_foodspotvendor_when')) {
    /**
     * Return a value if the given condition is true.
     *
     * @param  mixed  $condition
     * @param  \Closure|mixed  $value
     * @param  \Closure|mixed  $default
     * @return mixed
     */
    function onepix_foodspotvendor_when($condition, $value, $default = null)
    {
        if ($condition) {
            return onepix_foodspotvendor_value($value, $condition);
        }

        return onepix_foodspotvendor_value($default, $condition);
    }
}
