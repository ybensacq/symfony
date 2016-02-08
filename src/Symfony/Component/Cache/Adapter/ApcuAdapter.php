<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Cache\Adapter;

use Symfony\Component\Cache\Exception\CacheException;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ApcuAdapter extends AbstractAdapter
{
    public function __construct($namespace = '', $defaultLifetime = 0)
    {
        if (!function_exists('apcu_fetch') || !ini_get('apc.enabled') || ('cli' === PHP_SAPI && !ini_get('apc.enable_cli'))) {
            throw new CacheException('APCu is not enabled');
        }
        if ('cli' === PHP_SAPI) {
            ini_set('apc.use_request_time', 0);
        }
        parent::__construct($namespace, $defaultLifetime);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch(array $ids)
    {
        return apcu_fetch($ids);
    }

    /**
     * {@inheritdoc}
     */
    protected function doHave($id)
    {
        return apcu_exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doClear($namespace)
    {
        return apcu_clear_cache();
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete(array $ids)
    {
        foreach ($ids as $id) {
            apcu_delete($id);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave(array $values, $lifetime)
    {
        return array_keys(apcu_store($values, null, $lifetime));
    }
}
