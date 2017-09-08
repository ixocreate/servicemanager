<?php
declare(strict_types=1);
namespace KiwiSuite\ServiceManager\Resolver;


use KiwiSuite\ServiceManager\Exception\InvalidArgumentException;

class Resolution
{
    /**
     * @var string
     */
    private $serviceName;
    /**
     * @var array
     */
    private $dependencies;

    /**
     * ResolverResult constructor.
     * @param string $serviceName
     * @param array $dependencies
     */
    public function __construct(string $serviceName, array $dependencies)
    {
        $this->serviceName = $serviceName;
        $this->dependencies = $dependencies;

        $this->validateDependencies();
    }

    /**
     *
     */
    private function validateDependencies()
    {
        foreach ($this->dependencies as $dependency) {
            if (!\is_array($dependency)) {
                throw new InvalidArgumentException(
                    sprintf("Dependency should be an array, '%s' given in '%s'", gettype($dependency), $this->serviceName),
                    100
                );
            }

            if (!\array_key_exists("serviceName", $dependency) || !\is_string($dependency['serviceName'])) {
                throw new InvalidArgumentException(sprintf("Invalid 'serviceName' for dependency in '%s'", $this->serviceName), 200);
            }

            if (!\array_key_exists("subManager", $dependency) || ($dependency['subManager'] !== null && !\is_string($dependency['subManager']))) {
                throw new InvalidArgumentException(sprintf("Invalid 'subManager' for dependency in '%s'", $this->serviceName), 300);
            }
        }
    }

    /**
     * @return string
     */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }
}
