<?php

declare(strict_types=1);

namespace SlimAPI\Routing;

use Slim\Routing\Route as BaseRoute;

class Route extends BaseRoute
{
    protected array $attributes;

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return Route
     */
    public function setAttribute(string $name, $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Get route settings from route definition.
     * @return Settings
     * @php
     */
    public function getSettings(): Settings
    {
        return $this->getAttribute(RouteContext::ROUTE_SETTINGS, Settings::from([]));
    }
}
