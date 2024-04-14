<?php

namespace App\Http\Classes\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheTrait
{
    protected function getFromCache(string $key)
    {
        return Cache::get($key);
    }
    protected function putToCache(string $key, $value, $ttl = null): void
    {
        Cache::put($key, $value, $ttl);
    }
    protected function deleteFromCache(string $key): bool
    {
        return Cache::forget($key);
    }
    protected function hasInCache(string $key): bool
    {
        return Cache::has($key);
    }
}
