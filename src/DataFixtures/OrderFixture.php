<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\Seller;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
class OrderFixture extends Fixture implements DependentFixtureInterface
{
    const ORDER_COUNT = 100;
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < self::ORDER_COUNT; $i++) {
        	$order = new Order();

            $order->setOrderDate($faker->dateTimeBetween('-1 year', 'now'));
            $order->setDeliveryDate($faker->dateTimeBetween('now', '+1 year'));
            $order->setOrderStatus(Order::STATUS_PENDING);
            $order->setTotalAmount($faker->randomFloat(2, 0, 1000));
            $order->setCustomer($this->getReference('customer_' . $faker->numberBetween(0, 99)));

            $manager->persist($order);

            $this->addReference('order_' . $i, $order);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            CustomerFixture::class,
            SellerFixture::class,
        ];
    }
}
