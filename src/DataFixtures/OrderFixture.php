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
            $orderItems = $this->getReference('order_item_' . $i);
            $productCreatedAt = $orderItems->getProduct()->getCreatedAt();
            $createdAt = $faker->dateTimeBetween($productCreatedAt, 'now');
            $orderDate = $faker->dateTimeBetween($createdAt, 'now');

            for($j = 0; $j < 5; $j++) {
                $order->addOrderItem($this->getReference('order_item_' . $i));
            }

            $order->setCreatedAt($createdAt);
            $order->setOrderDate($orderDate);
            $order->setDeliveryDate($faker->dateTimeBetween($orderDate, '+1 month'));
            if ($order->getDeliveryDate() < new \DateTime('now')) {
                $order->setOrderStatus(Order::STATUS_DELIVERED);
            } else {
                $order->setOrderStatus(Order::STATUS_PENDING);
            }
            $order->setTotalAmount($faker->randomFloat(2, 0, 1000));
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
            OrderItemFixture::class
        ];
    }
}
