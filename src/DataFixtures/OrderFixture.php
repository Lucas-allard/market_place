<?php

namespace App\DataFixtures;

use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
class OrderFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
        	$order = new Order();
            $order->setOrderDate($faker->dateTimeBetween('-1 year', 'now'));
            $order->setDeliveryDate($faker->dateTimeBetween('now', '+1 year'));
            $order->setOrderStatus(Order::STATUS_PENDING);
            $order->setTotalAmount($faker->randomFloat(2, 0, 1000));

            $manager->persist($order);
        }

        $manager->flush();
    }
}
