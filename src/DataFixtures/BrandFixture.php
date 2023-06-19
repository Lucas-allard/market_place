<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Picture;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;

class BrandFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $faker = FakerFactory::create('fr_FR');

        $brandData = [
            'Électronique' => [
                'Téléphones' => ['Apple', 'Samsung', 'Sony'],
                'Ordinateurs' => ['HP', 'Dell'],
                'Télévisions' => ['Samsung', 'LG'],
                'Appareils photo' => ['Sony', 'Canon', 'Nikon'],
                'Audio' => ['Bose', 'Sony', 'JBL'],
                'Accessoires' => ['Belkin', 'Logitech', 'Anker'],
            ],
            'Mode' => [
                'Homme' => ['Nike', 'Adidas', 'Zara'],
                'Femme' => ['Zara', 'H&M', 'Gucci'],
                'Enfant' => ['Nike', 'Adidas', 'Disney'],
                'Accessoires' => ['Gucci', 'Prada'],
            ],
            'Maison' => [
                'Cuisine' => ['Ikea', 'Tefal', 'Moulinex'],
                'Décoration' => ['Ikea', 'Zara Home'],
                'Linge de maison' => ['Ikea', 'Leroy Merlin'],
                'Meubles' => ['Ikea', 'Leroy Merlin'],
                'Electroménager' => ['Bosch', 'Philips'],
                'Jardin' => ['Leroy Merlin', 'Truffaut'],
                'Bricolage' => ['Leroy Merlin', 'Castorama'],
            ],
            'Sport' => [
                'Vêtements' => ['Nike', 'Adidas', 'Under Armour'],
                'Chaussures' => ['Nike', 'Adidas', 'Reebok'],
                'Accessoires' => ['Decathlon', 'Nike', 'Puma'],
                'Equipements' => ['Decathlon', 'Salomon', 'Wilson'],
            ],
            'Loisirs' => [
                'Livres' => ['Fnac', 'Amazon', 'Cultura'],
                'Musique' => ['Fnac', 'Amazon'],
                'Films' => ['Fnac', 'Amazon'],
                'Jeux vidéo' => ['Steam', 'PlayStation', 'Xbox'],
                'Jeux et jouets' => ['Fnac', 'Toys "R" Us'],
                'Bricolage' => ['Leroy Merlin', 'Castorama'],
                'Jardin' => ['Leroy Merlin', 'Truffaut'],
            ],
            'Auto-Moto' => [
                'Pièces détachées' => ['Bosch', 'Valeo', 'Michelin'],
                'Equipements' => ['Norauto', 'Feu Vert'],
                'Accessoires' => ['Norauto', 'Feu Vert'],
            ],
        ];
        $i = 0;
        $j= 0;

        foreach ($brandData as $categoryName => $subCategories) {
            $category = $this->getReference('cat_' . $i);

            if (!$category) {
                continue; // La catégorie n'existe pas en base de données, passe à la suivante.
            }

            foreach ($subCategories as $subCategoryName => $brands) {
                $subCategory = $this->getReference('sub_cat_' . $j);

                if (!$subCategory) {
                    continue; // La sous-catégorie n'existe pas en base de données, passe à la suivante.
                }

                foreach ($brands as $brandName) {
// check if brand already exists, if true, add cat and subcat to existing brand else create new brand
                    $brand = $manager->getRepository(Brand::class)->findOneBy(['name' => $brandName]);
                    if (!$brand) {

                        $brandPicture = new Picture();
                        $brandPicture->setPath('https://picsum.photos/seed/' . $brandName . '/200/300');
                        $brandPicture->setAlt($brandName);
                        $manager->persist($brandPicture);

                        $brand = new Brand();
                        $brand->setName($brandName);
                        $brand->setPicture($brandPicture);
                        $brand->setCreatedAt($faker->dateTimeBetween('-6 months'));
                        $brand->setSlug($this->slugify($brandName));
                    }
                    $brand->addCategory($category);
                    $brand->addCategory($subCategory);
                    $brand->setUpdatedAt($faker->dateTimeBetween('-6 months'));
                    $manager->persist($brand);
                }
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
        return trim($string, '-');
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            CategoryFixture::class,
        ];
    }
}
