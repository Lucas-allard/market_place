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
                'Téléphones' => ['Apple', 'Samsung', 'Huawei', 'Xiaomi'],
                'Ordinateurs' => ['HP', 'Dell', 'Asus', 'Acer', 'Lenovo'],
                'Télévisions' => ['LG', 'Panasonic', 'Philips', 'TCL', 'Hisense'],
                'Appareils photo' => ['Sony', 'Canon', 'Nikon'],
                'Audio' => ['Bose', 'JBL', 'Beats'],
                'Accessoires' => ['Belkin', 'Logitech', 'Anker'],
            ],
            'Mode' => [
                'Homme' => ['Nike', 'Adidas', 'Zara'],
                'Femme' => ['H&M', 'Gucci', 'Prada'],
                'Accessoires' => ['Chanel', 'Dior', 'Hermès'],
            ],
            'Maison' => [
                'Cuisine' => ['Tefal', 'Moulinex', 'Bosch', 'Seb'],
                'Décoration' => ['Ikea', 'Zara Home', 'Maisons du Monde'],
                'Linge de maison' => ['Leroy Merlin', 'Castorama', 'Truffaut'],
                'Meubles' => ['Conforama', 'Alinéa', 'Fly'],
                'Electroménager' => ['Siemens', 'Whirlpool', 'Electrolux', 'Miele'],
                'Jardin' => ['Jardiland', 'Botanic', 'Gamm Vert'],
                'Bricolage' => ['Brico Dépôt', 'Bricomarché', 'Bricorama'],
            ],
            'Sport' => [
                'Chaussures' => ['Reebok', 'Asics', 'New Balance'],
                'Accessoires' => ['Decathlon', 'Salomon', 'Wilson'],
                'Equipements' => ['Intersport', 'Go Sport', 'Sport 2000'],
            ],
            'Loisirs' => [
                'Livres' => ['Fnac', 'Amazon', 'Cultura'],
                'Jeux vidéo' => ['Steam', 'PlayStation', 'Xbox'],
                'Jeux et jouets' => ['Fnac', 'Toys "R" Us'],
            ],
            'Auto-Moto' => [
                'Pièces détachées' => ['Valeo', 'Michelin', 'Castrol'],
                'Equipements' => ['Norauto', 'Feu Vert'],
            ],
        ];
        $i = 0;
        $j = 0;
        $k = 0;

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


                    $brandPicture = new Picture();
                    $brandPicture->setPath('https://picsum.photos/seed/' . $brandName . '/200/300');
                    $brandPicture->setAlt($brandName);
                    $manager->persist($brandPicture);

                    $brand = new Brand();
                    $brand->setName($brandName);
                    $brand->setPicture($brandPicture);
                    $brand->setCreatedAt($faker->dateTimeBetween('-6 months'));
                    $brand->setSlug($this->slugify($brandName));

                    $brand->addCategory($category);
                    $brand->addCategory($subCategory);
                    $brand->setUpdatedAt($faker->dateTimeBetween('-6 months'));
                    $manager->persist($brand);

                    $this->addReference('brand__' . $k, $brand);

                    $k++;
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
