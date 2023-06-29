<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
class OrderItemFixture extends Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) : void
    {
        $faker = Faker\Factory::create();

        for ($i = 0000; $i < 2000; $i++) {
            $orderItem = new OrderItem();
            $orderItem->setQuantity($faker->numberBetween(1, 10));

            /**
             * @var Product $product
             */
            $product = $this->getReference('product_' . $faker->numberBetween(0, 999));

            $order = $this->getReference('order_' . $faker->numberBetween(0, 499));
            $orderItem->setProduct($product);
            $orderItem->setOrder($order);
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
            ProductFixture::class,
            OrderFixture::class
        ];
    }
}