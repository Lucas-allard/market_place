<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CategoryFixture extends Fixture
{

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) : void
    {
        $faker = Faker\Factory::create('fr_FR');

        $categories = [
            'Électronique' => [
                'Téléphones',
                'Ordinateurs',
                'Télévisions',
                'Appareils photo',
                'Audio',
                'Accessoires',
            ],
            'Mode' => [
                'Homme',
                'Femme',
                'Enfant',
                'Accessoires',
            ],
            'Maison' => [
                'Cuisine',
                'Décoration',
                'Linge de maison',
                'Meubles',
                'Electroménager',
                'Jardin',
                'Bricolage',
            ],
            'Sport' => [
                'Vêtements',
                'Chaussures',
                'Accessoires',
                'Equipements',
            ],
            'Loisirs' => [
                'Livres',
                'Musique',
                'Films',
                'Jeux vidéo',
                'Jeux et jouets',
                'Bricolage',
                'Jardin',
                'Bricolage',
            ],
            'Auto-Moto' => [
                'Pièces détachées',
                'Equipements',
                'Accessoires',
            ],
            'Autres' => [
                'Autres',
            ],
        ];
        $i = 0;
        $j= 0;
        foreach ($categories as $categoryName => $subCategories) {

            $category = new Category();
            $category->setName($categoryName);
            $category->setDescription($faker->text(200));

            $this->addReference('cat_' . $i, $category);

            $manager->persist($category);

            foreach ($subCategories as $subCategoryName) {
                $subCategory = new Category();
                $subCategory->setName($subCategoryName);
                $subCategory->setDescription($faker->text(200));
                $subCategory->setParent($category);


                $this->addReference('sub_cat_' . $j, $subCategory);

                $manager->persist($subCategory);
                $j++;
            }
            $i++;
        }

        $manager->flush();
    }
}