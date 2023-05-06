<?php

namespace App\DataFixtures;

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
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 50; $i++) {
            $payment = new Payment();
            $order = $this->getReference('order_' . $i);
            $payment->setDescription($faker->text);
            $payment->setAmount($order->getTotalAmount());
            $payment->setStatus(Payment::STATUS_PAID);
            $payment->setOrder($order);

            $manager->persist($payment);

            $this->addReference('payment_' . $i, $payment);

        }

        for ($i = 50; $i < 100; $i++) {
            $payment = new Payment();
            $order = $this->getReference('order_' . $i);
            $payment->setDescription($faker->text);
            $payment->setAmount($order->getTotalAmount());
            $payment->setStatus(Payment::STATUS_PENDING);
            $payment->setOrder($order);

            $manager->persist($payment);
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