<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Picture;
use App\Entity\Product;
use App\Entity\Seller;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
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

        for ($i = 0; $i < 1000; $i++) {
            $productPictures = new ArrayCollection();
            for ($j = 0; $j < 3; $j++) {
                $picture = new Picture();
                $picture->setPath($faker->imageUrl(640, 480));
                $picture->setAlt($faker->sentence(3));
                $manager->persist($picture);
                $productPictures[] = $picture;
            }
            $product = new Product();

            for($k = 0; $k < 100; $k++) {
                $product->setDiscount($faker->numberBetween(0, 100));
            }

            $product->setName($faker->words(3, true));
            $product->setSlug($faker->slug);
            $product->setDescription($faker->text);
            $product->setCreatedAt($faker->dateTimeBetween('-1 year'));
            $product->setPrice($faker->randomFloat(2, 0, 1000));
            $product->setQuantity($faker->numberBetween(0, 1000));
            $product->setShippingFee($faker->randomFloat(2, 0, 20));

            $seller = $this->getReference('seller_' . $faker->numberBetween(0, 19));
            $product->setSeller($seller);

            $brand = $this->getReference('brand__' . $faker->numberBetween(0, 76));
            $product->setBrand($brand);
            $product->addCategory($brand->getCategories()->first());
            $product->addCategory($brand->getCategories()->last());
            $product->setPictures($productPictures);
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
            BrandFixture::class,
            SellerFixture::class,
        ];
    }
}