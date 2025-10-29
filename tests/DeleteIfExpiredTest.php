<?php

use voku\cache\AdapterArray;
use voku\cache\Cache;
use voku\cache\iAdapter;
use voku\cache\iSerializer;
use voku\cache\SerializerDefault;

/**
 * DeleteIfExpiredTest
 *
 * Test the new deleteIfExpired parameter in get() method
 *
 * @internal
 */
final class DeleteIfExpiredTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var iSerializer
     */
    public $serializer;

    /**
     * @var iAdapter
     */
    public $adapter;

    /**
     * @var Cache
     */
    public $cache;

    protected $backupGlobalsBlacklist = [
        '_SESSION',
    ];

    public function testGetWithIgnoreTtlFalse()
    {
        // Set an item with 1 second TTL
        $return = $this->cache->setItem('test_key', 'test_value', 1);
        static::assertTrue($return);

        // Get item immediately - should exist
        $return = $this->cache->getItem('test_key');
        static::assertSame('test_value', $return);

        // Wait for expiration
        \sleep(2);

        // Get with ignoreTtl=false (default) - should return null and delete the item
        $return = $this->adapter->get('test_key', false);
        static::assertNull($return);

        // Verify the item was deleted
        $return = $this->adapter->exists('test_key');
        static::assertFalse($return);
    }

    public function testGetWithIgnoreTtlTrue()
    {
        // Set an item with 1 second TTL
        $return = $this->cache->setItem('test_key2', 'test_value2', 1);
        static::assertTrue($return);

        // Get item immediately - should exist
        $return = $this->cache->getItem('test_key2');
        static::assertSame('test_value2', $return);

        // Wait for expiration
        \sleep(2);

        // Get with ignoreTtl=true - should return the value even though expired and NOT delete the item
        $serialized = $this->adapter->get('test_key2', true);
        static::assertNotNull($serialized, 'Serialized value should not be null when ignoreTtl=true');
        $return = $this->serializer->unserialize($serialized);
        static::assertSame('test_value2', $return);

        // With AdapterArray, we can check that the value is still in storage
        assert($this->adapter instanceof AdapterArray);
        $keys = $this->adapter->getStaticKeys();
        static::assertTrue(\in_array('test_key2', $keys), 'Key should still exist in storage when ignoreTtl=true');
    }

    public function testGetWithDefaultBehavior()
    {
        // Set an item with 1 second TTL
        $return = $this->cache->setItem('test_key3', 'test_value3', 1);
        static::assertTrue($return);

        // Wait for expiration
        \sleep(2);

        // Get without specifying ignoreTtl - should use default (false) and delete, returning null
        $return = $this->adapter->get('test_key3');
        static::assertNull($return);

        // Verify the item was deleted (default behavior)
        $return = $this->adapter->exists('test_key3');
        static::assertFalse($return);
    }

    /**
     * @before
     */
    protected function setUpThanksForNothing()
    {
        $this->adapter = new AdapterArray();
        $this->serializer = new SerializerDefault();

        $this->cache = new Cache($this->adapter, $this->serializer, false, true);

        // reset default prefix
        $this->cache->setPrefix('');

        // Clear all cache to ensure clean state
        $this->adapter->removeAll();
    }
}
