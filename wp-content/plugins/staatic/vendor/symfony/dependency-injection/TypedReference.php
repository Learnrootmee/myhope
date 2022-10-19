<?php

namespace Staatic\Vendor\Symfony\Component\DependencyInjection;

class TypedReference extends Reference
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var string|null
     */
    private $name;
    public function __construct(string $id, string $type, int $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, string $name = null)
    {
        $this->name = $type === $id ? $name : null;
        parent::__construct($id, $invalidBehavior);
        $this->type = $type;
    }
    public function getType()
    {
        return $this->type;
    }
    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }
}
