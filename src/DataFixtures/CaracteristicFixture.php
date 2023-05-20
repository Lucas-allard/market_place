<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Caracteristic;
use App\Entity\Category;
use App\Entity\Picture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CaracteristicFixture extends Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 1000; $i++) {

            $caracteristic = new Caracteristic();
            $caracteristic->setContent($faker->sentence(6, true));
            $caracteristic->setProduct($this->getReference('product_' . $i));

            $manager->persist($caracteristic);

        }

        for ($i = 0; $i < 1000; $i++) {

            $caracteristic = new Caracteristic();
            $caracteristic->setContent($faker->sentence(6, true));
            $caracteristic->setProduct($this->getReference('product_' . $i));

            $manager->persist($caracteristic);


        }

        for ($i = 0; $i < 1000; $i++) {

            $caracteristic = new Caracteristic();
            $caracteristic->setContent($faker->sentence(6, true));
            $caracteristic->setProduct($this->getReference('product_' . $i));

            $manager->persist($caracteristic);

        }

        $manager->flush();
    }


    /**
     * @return string[]
     */
    public function getDependencies()
    {
        return [
            ProductFixture::class,
        ];
    }
}