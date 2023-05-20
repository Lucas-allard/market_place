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

        for ($i = 0; $i < 1000; $i++) {
            $orderItem = new OrderItem();
            $orderItem->setQuantity($faker->numberBetween(1, 10));
            $orderItem->setPrice($faker->randomFloat(2, 0, 1000));

            /**
             * @var Product $product
             */
            $product = $this->getReference('product_' . $faker->numberBetween(0, 999));


            $orderItem->setProduct($product);
            $manager->persist($orderItem);

            $this->addReference('order_item_' . $i, $orderItem);
        }

        for ($i = 1000; $i < 2000; $i++) {
            $orderItem = new OrderItem();
            $orderItem->setQuantity($faker->numberBetween(1, 10));
            $orderItem->setPrice($faker->randomFloat(2, 0, 1000));

            /**
             * @var Product $product
             */
            $product = $this->getReference('product_' . $faker->numberBetween(0, 999));


            $orderItem->setProduct($product);
            $manager->persist($orderItem);

            $this->addReference('order_item_' . $i, $orderItem);
        }

        $manager->flush();
    }



    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            ProductFixture::class
        ];
    }
}