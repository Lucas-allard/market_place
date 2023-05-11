<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Seller;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ProductFixture extends Fixture implements DependentFixtureInterface
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) : void
    {
        $faker = Faker\Factory::create();

        for ($i = 0; $i < 100; $i++) {
            $product = new Product();
            $product->setName($faker->words(3, true));
            $product->setSlug($faker->slug);
            $product->setDescription($faker->text);
            $product->setPrice($faker->randomFloat(2, 0, 1000));
            $product->setQuantity($faker->numberBetween(0, 1000));
            /** @var Seller $seller */
            $seller = $this->getReference('seller_' . $faker->numberBetween(0, 19));
            $product->setSeller($seller);
            /** @var Category $category */
            $category = $this->getReference('cat_' . $faker->numberBetween(0, 5));
            /** @var Category $subCategory */
            $subCategory = $this->getReference('sub_cat_' . $faker->numberBetween(0, 29));
            $product->addCategory($category);
            $product->addCategory($subCategory);
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