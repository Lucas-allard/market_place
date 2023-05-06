<?php

namespace App\DataFixtures;

use App\Entity\OrderItem;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
class OrderItemFixture extends Fixture implements DependentFixtureInterface
{
    const ORDER_ITEM_COUNT = 100;

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < self::ORDER_ITEM_COUNT; $i++) {
            $orderItem = new OrderItem();
            $orderItem->setQuantity($faker->numberBetween(1, 10));
            $orderItem->setPrice($faker->randomFloat(2, 0, 1000));
            $orderItem->setProduct($this->getReference('product_' . $faker->numberBetween(0, 99)));
            $orderItem->setOrder($this->getReference('order_' . $faker->numberBetween(0, 99)));
            $manager->persist($orderItem);
        }

        $manager->flush();
    }



    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            OrderFixture::class,
            ProductFixture::class
        ];
    }
}