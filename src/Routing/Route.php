<?php

declare(strict_types=1);

namespace SlimAPI\Routing;

class Route extends \Slim\Routing\Route
{
    protected array $attributes;

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    public function setAttribute(string $name, mixed $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Get route settings from route definition.
     */
    public function getSettings(): Settings
    {
        return $this->getAttribute(RouteContext::ROUTE_SETTINGS, Settings::from([]));
    }
}
