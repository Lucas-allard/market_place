<?php

namespace App\DataFixtures;


use App\Entity\Caracteristic;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CaracteristicFixture extends Fixture implements DependentFixtureInterface
{
    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager): void
    {
        $caracteristicCategories = [
            'Électronique' => [
                'Système d\'exploitation',
                'Processeur',
                'Mémoire RAM',
                'Espace de stockage',
                'Résolution d\'écran',
                'Connectivité',
            ],
            'Mode' => [
                'Taille',
                'Matière',
                'Couleur',
                'Style',
            ],
            'Maison' => [
                'Matériau',
                'Couleur',
                'Capacité',
                'Dimensions',
                'Utilisation',
            ],
            'Sport' => [
                'Taille',
                'Couleur',
                'Matière',
                'Type de sport',
                'Niveau de pratique',
            ],
            'Loisirs' => [
                'Langue',
                'Format',
                'Genre',
                'Plateforme',
                'Âge recommandé',
            ],
            'Auto-Moto' => [
                'Marque',
                'Modèle',
                'Année',
                'Puissance',
                'Carburant',
            ],
        ];

        $caracteristicsValues = [
            'Système d\'exploitation' => ['iOS', 'Android', 'Windows'],
            'Processeur' => ['Intel Core i7', 'AMD Ryzen 7', 'Qualcomm Snapdragon'],
            'Mémoire RAM' => ['8 Go', '16 Go', '32 Go'],
            'Espace de stockage' => ['256 Go', '512 Go', '1 To'],
            'Résolution d\'écran' => ['Full HD', '4K Ultra HD', 'QHD'],
            'Connectivité' => ['Wi-Fi', 'Bluetooth', 'NFC'],
            'Taille' => ['S', 'M', 'L', 'XL'],
            'Matière' => ['Coton', 'Polyester', 'Cuir', 'Acrylique'],
            'Couleur' => ['Rouge', 'Bleu', 'Vert', 'Noir', 'Blanc'],
            'Style' => ['Décontracté', 'Formel', 'Sportif'],
            'Matériau' => ['Acier', 'Bois', 'Verre', 'Plastique'],
            'Capacité' => ['500 ml', '1 L', '2 L'],
            'Dimensions' => ['50 cm x 70 cm', '120 cm x 180 cm', '200 cm x 300 cm'],
            'Utilisation' => ['Cuisine', 'Salle de bain', 'Chambre à coucher'],
            'Type de sport' => ['Football', 'Basketball', 'Tennis', 'Natation'],
            'Niveau de pratique' => ['Débutant', 'Intermédiaire', 'Avancé'],
            'Langue' => ['Français', 'Anglais', 'Espagnol', 'Allemand'],
            'Format' => ['Broché', 'Relié', 'Numérique'],
            'Genre' => ['Roman', 'Science-fiction', 'Thriller', 'Fantasy'],
            'Plateforme' => ['PlayStation', 'Xbox', 'Nintendo Switch'],
            'Âge recommandé' => ['3+', '7+', '12+', '16+', '18+'],
            'Marque' => ['Toyota', 'Ford', 'BMW', 'Honda'],
            'Modèle' => ['Corolla', 'Focus', 'Serie 3', 'Civic'],
            'Année' => ['2019', '2020', '2021', '2022'],
            'Puissance' => ['150 ch', '200 ch', '250 ch', '300 ch'],
            'Carburant' => ['Essence', 'Diesel', 'Hybride'],
        ];

        foreach ($caracteristicsValues as $type => $values) {
            foreach ($values as $value) {
                $caracteristic = new Caracteristic();
                $caracteristic->setType($type);
                $caracteristic->setValue($value);

                $manager->persist($caracteristic);

                $this->addReference('caracteristic_' . $type . '_' . $value, $caracteristic);
            }

        }

        $manager->flush();

        // Ajout des caractéristiques aux produits
        $faker = \Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 1000; $i++) {
            $product = $this->getReference('product_' . $i);

            $categories = $product->getCategories();

            foreach ($categories as $category) {
                if (isset($caracteristicCategories[$category->getName()])) {
                    $caracteristicTypes = $caracteristicCategories[$category->getName()];

                    $numCharacteristics = $faker->numberBetween(2, 4);
                    $selectedTypes = $faker->randomElements($caracteristicTypes, $numCharacteristics);

                    foreach ($selectedTypes as $type) {
                        if (isset($caracteristicsValues[$type])) {
                            $values = $caracteristicsValues[$type];
                            $value = $faker->randomElement($values);

                            $caracteristic = $this->getReference('caracteristic_' . $type . '_' . $value);

                            $product->addCaracteristic($caracteristic);
                        }
                    }
                }
            }

            $manager->persist($product);
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies(): array
    {
        return [
            ProductFixture::class,
        ];
    }
}
