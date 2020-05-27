<?php

declare(strict_types=1);

namespace SlimAPI\DI;

use Nette\DI\Container;
use Psr\Container\ContainerInterface;

/**
 * @method array getParameters()
 * @method Container addService(string $name, object $service) Adds the service to the container.
 * @method void removeService(string $name) Removes the service from the container.
 * @method mixed getService(string $name) Gets the service object by name.
 * @method string getServiceType(string $name) Gets the service type by name.
 * @method bool hasService(string $name) Does the service exist?
 * @method bool isCreated(string $name) Is the service created?
 * @method mixed createService(string $name, array $args = []) Creates new instance of the service.
 * @method mixed getByType(string $type, $throw = true) Resolves service by type.
 * @method string[] findByType(string $type) Gets the service names of the specified type.
 * @method string findByTag(string $tag) Gets the service names of the specified tag.
 */
class ContainerAdapter implements ContainerInterface
{
    private string $prefix;

    public Container $container;

    public function __construct(string $prefix, Container $container)
    {
        $this->prefix = $prefix;
        $this->container = $container;
    }

    /**
     * @param mixed $id
     * @return mixed|object
     */
    public function get($id)
    {
        return $this->container->getService($this->prefix($id));
    }

    /**
     * @param mixed $id
     * @return bool
     */
    public function has($id): bool
    {
        return $this->container->hasService($this->prefix($id));
    }

    private function prefix(string $id): string
    {
        return $this->prefix . '.' . $id;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        return $this->container->$name(...$arguments);
    }
}
