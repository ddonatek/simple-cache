<?php

declare(strict_types=1);

namespace voku\cache;

use DateTimeInterface;

/**
 * iCache: cache-global interface
 */
interface iCache
{
    /**
     * get item
     *
     * @param string $key
     * @param int    $forceStaticCacheHitCounter  <p>Force static cache hit counter (implementation-specific)</p>
     * @param bool   $deleteIfExpired  <p>If true, delete the cache entry if it's expired (default: true)</p>
     *
     * @return mixed
     */
    public function getItem(string $key, int $forceStaticCacheHitCounter = 0, bool $deleteIfExpired = true);

    /**
     * set item
     *
     * @param string   $key
     * @param mixed    $value
     * @param int|null $ttl
     *
     * @return bool
     */
    public function setItem(string $key, $value, $ttl = 0): bool;

    /**
     * set item a special expire-date
     *
     * @param string             $key
     * @param mixed              $value
     * @param DateTimeInterface $date
     *
     * @return bool
     */
    public function setItemToDate(string $key, $value, DateTimeInterface $date): bool;

    /**
     * remove item
     *
     * @param string $key
     *
     * @return bool
     */
    public function removeItem(string $key): bool;

    /**
     * remove all items
     *
     * @return bool
     */
    public function removeAll(): bool;

    /**
     * check if item exists
     *
     * @param string $key
     *
     * @return bool
     */
    public function existsItem(string $key): bool;
}
