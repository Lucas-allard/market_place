<?php

namespace App\Tests\Entity;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

class AbstractEntityTest extends TestCase
{
    private $entity;
    private ReflectionClass $reflection;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->entity = $this->getMockForAbstractClass('App\Entity\AbstractEntity');
        $this->reflection = new ReflectionClass($this->entity);

    }

    /**
     * @throws ReflectionException
     */
    public function testGetId()
    {

        $property = $this->reflection->getProperty('id');
        $property->setValue($this->entity, 1);

        $this->assertIsInt($this->entity->getId());
        $this->assertEquals(1, $this->entity->getId());
    }

    /**
     * @throws ReflectionException
     */
    public function testGetCreatedAt()
    {
        $property = $this->reflection->getProperty('createdAt');
        $property->setValue($this->entity, new \DateTime());

        $this->assertInstanceOf(\DateTime::class, $this->entity->getCreatedAt());
        $this->assertEquals((new \DateTime())->format('Y-m-d H:i:s'), $this->entity->getCreatedAt()->format('Y-m-d H:i:s'));
    }

    /**
     * @throws ReflectionException
     */
    public function testGetUpdatedAt()
    {
        $property = $this->reflection->getProperty('updatedAt');
        $property->setValue($this->entity, new \DateTime());

        $this->assertInstanceOf(\DateTime::class, $this->entity->getUpdatedAt());
        $this->assertEquals((new \DateTime())->format('Y-m-d H:i:s'), $this->entity->getUpdatedAt()->format('Y-m-d H:i:s'));
    }
}