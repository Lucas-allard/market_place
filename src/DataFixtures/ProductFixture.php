<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ProductFixture extends Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 100; $i++) {
            $product = new Product();
            $product->setName($faker->name);
            $product->setDescription($faker->text);
            $product->setPrice($faker->randomFloat(2, 0, 1000));
            $product->setQuantity($faker->numberBetween(0, 1000));
            $product->setSeller($this->getReference('seller_' . $faker->numberBetween(0, 19)));
            $product->addCategory($this->getReference('cat_' . $faker->numberBetween(0, 6)));
            $product->addCategory($this->getReference('sub_cat_' . $faker->numberBetween(0, 30)));
            $manager->persist($product);

            $this->addReference('product_' . $i, $product);
        }

        $manager->flush();
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            CategoryFixture::class,
            SellerFixture::class,
        ];
    }
}