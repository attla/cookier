<?php

namespace Attla\Cookier;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class Manager extends \ArrayObject
{
    /**
     * Request instance
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Cookie prefix name
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Set the request instance
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Add the prefix if there is none
     *
     * @param string $key
     * @return string
     */
    public function withPrefix(string $name): string
    {
        $name = Str::slug($name, '_');

        if (!Str::startsWith($name, $this->prefix)) {
            $name = $this->prefix . $name;
        }

        return $name;
    }

    /**
     * Set token prefix
     *
     * @param string $prefix
     * @return self
     */
    public function setPrefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Get the prefix
     *
     * @return string
     */
    public function prefix(): string
    {
        return $this->prefix;
    }

    /**
     * Determine if a cookie exists on the request
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return !is_null($this->get($name, null, true));
    }

    /**
     * Alias for has
     *
     * @param string $name
     * @return bool
     */
    public function exists(string $name): bool
    {
        return $this->has($name);
    }

    /**
     * Retrieve a cookie from the request
     *
     * @param array|string|null $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name = null, $default = null, $original = false)
    {
        if (is_array($name)) {
            return $this->getMany($name);
        }

        if (is_null($name)) {
            return $this->all();
        }

        $value = $this->request->cookie($this->withPrefix($name))
            ?: $this->request->cookie($name);

        $value = $original ? $value : $this->value($value);

        return $value ?? $default;
    }

    /**
     * Retrieve many cookies
     *
     * @param array $keys
     * @return array
     */
    public function getMany(array $keys): array
    {
        $cookies = [];

        foreach ($keys as $name => $default) {
            if (is_numeric($name)) {
                [$name, $default] = [$default, null];
            }

            $cookies[$name] = $this->get($name, $default);
        }

        return $cookies;
    }

    /**
     * Retrieve a plain text cookie from the request
     *
     * @param array|string|null $name
     * @param mixed $default
     * @return string|null
     */
    public function getOriginal($name = null, $default = null)
    {
        return $this->get($name, $default, true);
    }

    /**
     * Retrieve all cookies
     *
     * @return array
     */
    public function all()
    {
        $cookies = $this->request->cookies->all();

        return array_combine(
            array_map(
                fn($key) => ltrim($key, $this->prefix),
                array_keys($cookies)
            ),
            array_map(
                fn($value) => $this->value($value),
                $cookies
            )
        );
    }

    /**
     * Resolve a cookie value
     *
     * @param mixed $value
     * @return mixed
     */
    public function value($value)
    {
        if ($jwtDecoded = \DataToken::decode($value)) {
            return $jwtDecoded;
        }

        return $value;
    }

    /**
     * Set a cookie value
     *
     * @param string $name
     * @param string $value
     * @param int $minutes
     * @param string|null $path
     * @param string|null $domain
     * @param bool|null $secure
     * @param bool $httpOnly
     * @param bool $raw
     * @param string|null $sameSite
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function set(
        $name,
        $value,
        $minutes = 30,
        $path = null,
        $domain = null,
        $secure = null,
        $httpOnly = true,
        $raw = false,
        $sameSite = null
    ) {
        $name = $this->withPrefix($name);
        $this->request->cookies->set($name, $value);

        Cookie::queue($cookie = Cookie::make(
            $name,
            $value,
            $minutes,
            $path,
            $domain,
            $secure,
            $httpOnly,
            $raw,
            $sameSite
        ));
        return $cookie;
    }

    /**
     * Alias for set
     *
     * @param string $name
     * @param string $value
     * @param int $minutes
     * @param string|null $path
     * @param string|null $domain
     * @param bool|null $secure
     * @param bool $httpOnly
     * @param bool $raw
     * @param string|null $sameSite
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function store(
        $name,
        $value,
        $minutes = 30,
        $path = null,
        $domain = null,
        $secure = null,
        $httpOnly = true,
        $raw = false,
        $sameSite = null
    ) {
        return $this->set(
            $name,
            $value,
            $minutes,
            $path,
            $domain,
            $secure,
            $httpOnly,
            $raw,
            $sameSite
        );
    }

    /**
     * Create a cookie that lasts "forever" (five years)
     *
     * @param string $name
     * @param string $value
     * @param string|null $path
     * @param string|null $domain
     * @param bool|null $secure
     * @param bool $httpOnly
     * @param bool $raw
     * @param string|null $sameSite
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function forever(
        $name,
        $value,
        $path = null,
        $domain = null,
        $secure = null,
        $httpOnly = true,
        $raw = false,
        $sameSite = null
    ) {
        return $this->set(
            $name,
            $value,
            2628000,
            $path,
            $domain,
            $secure,
            $httpOnly,
            $raw,
            $sameSite
        );
    }

    /**
     * Forget a cookie by name
     *
     * @param string $name
     * @param string|null $path
     * @param string|null $domain
     * @return void
     */
    public function forget(string $name, $path = null, $domain = null)
    {
        $this->request->cookies->remove($name);
        Cookie::queue(Cookie::forget($name, $path, $domain));

        $name = $this->withPrefix($name);
        $this->request->cookies->remove($name);
        Cookie::queue(Cookie::forget($name, $path, $domain));
    }

    /**
     * Alias for forget
     *
     * @param string $name
     * @param string|null $path
     * @param string|null $domain
     * @return void
     */
    public function delete(string $name, $path = null, $domain = null)
    {
        $this->forget($name, $path, $domain);
    }

    /**
     * Alias for forget
     *
     * @param string $name
     * @param string|null $path
     * @param string|null $domain
     * @return void
     */
    public function unset(string $name, $path = null, $domain = null)
    {
        $this->forget($name, $path, $domain);
    }

    /**
     * Alias for forget
     *
     * @param string $name
     * @param string|null $path
     * @param string|null $domain
     * @return void
     */
    public function expire(string $name, $path = null, $domain = null)
    {
        $this->forget($name, $path, $domain);
    }

    /**
     * Alias for forget
     *
     * @param string $name
     * @param string|null $path
     * @param string|null $domain
     * @return void
     */
    public function destroy(string $name, $path = null, $domain = null)
    {
        $this->forget($name, $path, $domain);
    }

    /**
     * Unqueue a cookie by name
     *
     * @param string $name
     * @param string|null $path
     * @return void
     */
    public function unqueue(string $name, $path = null)
    {
        Cookie::unqueue($this->withPrefix($name), $path);
    }

    /**
     * Determine if a cookie has been queued
     *
     * @param string $key
     * @param string|null $path
     * @return bool
     */
    public function hasQueued(string $key, $path = null)
    {
        return Cookie::hasQueued($this->withPrefix($key), $path);
    }

    /**
     * Determine if the given cookie exists
     *
     * @param string $key
     * @return bool
     */
    public function offsetExists($key): bool
    {
        return $this->has($key);
    }

    /**
     * Get a cookie
     *
     * @param string $key
     * @return mixed
     */
    public function offsetGet($key): mixed
    {
        return $this->get($key);
    }

    /**
     * Set a cookie
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function offsetSet($key, $value): void
    {
        $this->set($key, $value);
    }

    /**
     * Unset a cookie
     *
     * @param string $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        $this->forget($key);
    }

    public function __isset($name)
    {
        return $this->has($name);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    public function __unset($name)
    {
        $this->forget($name);
    }

    public function __call($method, $parameters)
    {
        return Cookie::{$method}(...$parameters);
    }
}
