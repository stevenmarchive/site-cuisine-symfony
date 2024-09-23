<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

// faker
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{

    // Déclaration de la propriété $faker qui servira à générer des données aléatoires
    private Generator $faker;

    // Constructeur de la classe, où l'objet $faker est initialisé
    public function __construct()
    {

        // Initialisation de Faker pour générer des données en français (fr_FR)
        $this->faker = Factory::create('fr_FR');
    }

    // Méthode load qui va charger des données (fixtures) dans la base de données
    public function load(ObjectManager $manager): void
    {

        // Boucle qui va créer 50 objets Ingredient
        for ($i = 1; $i <= 10; $i++) {

            // Création d'une nouvelle instance de l'entité Ingredient
            $ingredient = new Ingredient();

            // Génération d'un nom d'ingrédient aléatoire avec Faker
            $ingredient->setnom($this->faker->word());

            // Définition du prix de l'ingrédient avec une valeur aléatoire entre 0 et 100
            $ingredient->setprix(mt_rand(0, 100));

            // Prépare l'objet Ingredient pour l'insertion dans la base de données
            $manager->persist($ingredient);
        }

        // Exécute toutes les insertions dans la base de données
        $manager->flush();
    }
}
