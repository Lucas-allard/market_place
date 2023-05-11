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
        ];
        $i = 0;
        $j= 0;
        foreach ($categories as $categoryName => $subCategories) {

            $category = new Category();
            $category->setName($categoryName);
            $category->setDescription($faker->text(200));
            $slug = $this->slugify($categoryName);
            $category->setSlug($slug);
            $this->addReference('cat_' . $i, $category);

            $manager->persist($category);

            foreach ($subCategories as $subCategoryName) {
                $subCategory = new Category();
                $subCategory->setName($subCategoryName);
                $subCategory->setDescription($faker->text(200));
                $subCategory->setParent($category);
                $slug = $this->slugify($subCategoryName);
                $subCategory->setSlug($slug);

                $this->addReference('sub_cat_' . $j, $subCategory);

                $manager->persist($subCategory);
                $j++;
            }
            $i++;
        }

        $manager->flush();
    }
    function slugify($string): string
    {
        // Conversion en minuscules et suppression des espaces avant et après
        $string = mb_strtolower(trim($string));


        // Tableau de correspondance pour les caractères spéciaux
        $char_map = array(
            // Lettres avec accents
            'à' => 'a', 'â' => 'a', 'ä' => 'a', 'á' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'é' => 'e', 'ï' => 'i', 'î' => 'i', 'ì' => 'i',
            'í' => 'i', 'ñ' => 'n', 'ô' => 'o', 'ö' => 'o', 'ò' => 'o', 'ó' => 'o', 'œ' => 'oe',
            'ß' => 'ss', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ú' => 'u',
            // Caractères spéciaux
            '&' => 'et', '@' => 'at', '%' => 'pourcent',
        );

        // Remplacement des caractères spéciaux
        $string = str_replace(array_keys($char_map), $char_map, $string);

        // Remplacement des caractères non alpha-numériques par un tiret
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);

        // Remplacement des tirets consécutifs par un seul tiret
        $string = preg_replace('/-+/', '-', $string);

        // Suppression des tirets en début et fin de chaîne
        $string = trim($string, '-');

        return $string;
    }


}