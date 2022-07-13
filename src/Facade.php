<?php

namespace Attla\Cookier;

use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * @method static void setRequest(\Illuminate\Http\Request $request)
 * @method static string withPrefix(string $name)
 * @method static void setPrefix(string $prefix)
 * @method static string prefix()
 * @method static bool has(string $name)
 * @method static bool exists(string $name)
 * @method static mixed get($name = null, $default = null, $original = false)
 * @method static array getMany(array $keys)
 * @method static string|null getOriginal($name = null, $default = null)
 * @method static array all()
 * @method static mixed value($value)
 * @method static \Symfony\Component\HttpFoundation\Cookie set($name, $value, $minutes = 30, $path = null,  $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
 * @method static \Symfony\Component\HttpFoundation\Cookie store($name, $value, $minutes = 30, $path = null,  $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
 * @method static \Symfony\Component\HttpFoundation\Cookie forever($name, $value, $path = null,  $domain = null, $secure = null, $httpOnly = true, $raw = false, $sameSite = null)
 * @method static void forget(string $name, $path = null, $domain = null)
 * @method static void delete(string $name, $path = null, $domain = null)
 * @method static void unset(string $name, $path = null, $domain = null)
 * @method static void destroy(string $name, $path = null, $domain = null)
 * @method static void expire(string $name, $path = null, $domain = null)
 * @method static void unqueue(string $name, string|null $path)
 * @method static bool hasQueued(string $key, $path = null)
 *
 * @see \Attla\Cookier\Manager
 */
class Facade extends BaseFacade
{
    /**
     * Get the registered name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cookier';
    }
}
