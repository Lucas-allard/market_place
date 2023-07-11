<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Seller;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class OrderFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 500; $i++) {
            $order = new Order();
            $createdAt = $faker->dateTimeBetween('-1 year', '-1 month');
            $orderDate = $faker->dateTimeBetween($createdAt, 'now');


            $order->setCreatedAt($createdAt);
            $order->setOrderDate($orderDate);
            if ($order->getOrderDate() < $faker->dateTimeBetween('-2 month', 'now')) {
                $order->setStatus(Order::STATUS_COMPLETED);
            } else {
                $order->setStatus(Order::STATUS_CART);
            }
            /** @var Customer $customer */
            $customer = $this->getReference('customer_' . $faker->numberBetween(0, 99));
            $order->setCustomer($customer);

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
