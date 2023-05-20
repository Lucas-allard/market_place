<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Picture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class BrandFixture extends Fixture
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) : void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
            $brandPicture = new Picture();
            $brandPicture->setPath($faker->imageUrl(640, 480));
            $brandPicture->setAlt($faker->sentence(3));
            $manager->persist($brandPicture);

            $brand = new Brand();
            $brand->setName($faker->company);
            $brand->setSlug($faker->slug);
            $brand->setPicture($brandPicture);
            $manager->persist($brand);

            $this->addReference('brand_' . $i, $brand);
        }

        $manager->flush();
    }


}