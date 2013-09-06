<?php

namespace JaztecAcl\Cache;

use Zend\Cache\Storage\StorageInterface;

interface CacheAwareInterface
{

    /**
     * @param \Zend\Cache\Storage\StorageInterface $storage
     */
    public function setCacheStorage(StorageInterface $storage);

    /**
     * @return \Zend\Cache\Storage\StorageInterface
     */
    public function getCacheStorage();
}
