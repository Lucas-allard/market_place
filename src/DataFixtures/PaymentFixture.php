<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\Payment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class PaymentFixture extends Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 500; $i++) {
            $payment = new Payment();

            /**
             * @var Order $order
             */
            $order = $this->getReference('order_' . $i);
            $payment->setDescription($faker->text);
            $payment->setAmount($order->getTotal());
            if ($order->getStatus() === Order::STATUS_CART) {
                $payment->setStatus(Payment::STATUS_PENDING);
            } elseif ($order->getStatus() === Order::STATUS_COMPLETED) {
                $payment->setStatus(Payment::STATUS_PAID);
            }
            $payment->setStatus(Payment::STATUS_PAID);
            $payment->setOrder($order);

            $manager->persist($payment);

            $this->addReference('payment_' . $i, $payment);

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
        ];
    }
}