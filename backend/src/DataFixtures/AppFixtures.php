<?php

namespace App\DataFixtures;

use App\Entity\Marque;
use App\Entity\Vehicule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $marques = [
            ['nom' => 'Ferrari',     'anneeCreation' => 1947, 'pays' => 'Italie'],
            ['nom' => 'Lamborghini', 'anneeCreation' => 1963, 'pays' => 'Italie'],
            ['nom' => 'Porsche',     'anneeCreation' => 1931, 'pays' => 'Allemagne'],
            ['nom' => 'McLaren',     'anneeCreation' => 1963, 'pays' => 'Royaume-Uni'],
            ['nom' => 'Bugatti',     'anneeCreation' => 1909, 'pays' => 'France'],
        ];

        // Images Unsplash (CORS: Access-Control-Allow-Origin: *)
        $vehicules = [
            [
                'modele'    => 'Ferrari 488 GTB',
                'prix'      => 250000,
                'puissance' => 660,
                'annee'     => 2015,
                'marque'    => 0,
                'photo'     => 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'modele'    => 'Ferrari SF90 Stradale',
                'prix'      => 500000,
                'puissance' => 1000,
                'annee'     => 2020,
                'marque'    => 0,
                'photo'     => 'https://images.unsplash.com/photo-1616422285623-13ff0162193c?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'modele'    => 'Ferrari 296 GTB',
                'prix'      => 320000,
                'puissance' => 830,
                'annee'     => 2022,
                'marque'    => 0,
                'photo'     => 'https://images.unsplash.com/photo-1605559424843-9e4c228bf1c2?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'modele'    => 'Lamborghini Huracán',
                'prix'      => 230000,
                'puissance' => 640,
                'annee'     => 2014,
                'marque'    => 1,
                'photo'     => 'https://images.unsplash.com/photo-1571607388263-1044f9ea0665?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'modele'    => 'Lamborghini Urus',
                'prix'      => 200000,
                'puissance' => 650,
                'annee'     => 2018,
                'marque'    => 1,
                'photo'     => 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'modele'    => 'Lamborghini Revuelto',
                'prix'      => 600000,
                'puissance' => 1015,
                'annee'     => 2023,
                'marque'    => 1,
                'photo'     => 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'modele'    => 'Porsche 911 GT3',
                'prix'      => 175000,
                'puissance' => 510,
                'annee'     => 2021,
                'marque'    => 2,
                'photo'     => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'modele'    => 'Porsche Taycan Turbo S',
                'prix'      => 190000,
                'puissance' => 761,
                'annee'     => 2022,
                'marque'    => 2,
                'photo'     => 'https://images.unsplash.com/photo-1611821064430-0d40291d0f0b?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'modele'    => 'McLaren 720S',
                'prix'      => 280000,
                'puissance' => 720,
                'annee'     => 2017,
                'marque'    => 3,
                'photo'     => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'modele'    => 'McLaren Artura',
                'prix'      => 230000,
                'puissance' => 680,
                'annee'     => 2022,
                'marque'    => 3,
                'photo'     => 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'modele'    => 'Bugatti Chiron',
                'prix'      => 3000000,
                'puissance' => 1500,
                'annee'     => 2016,
                'marque'    => 4,
                'photo'     => 'https://images.unsplash.com/photo-1527824404775-dce343159efc?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'modele'    => 'Bugatti Veyron',
                'prix'      => 1700000,
                'puissance' => 1001,
                'annee'     => 2005,
                'marque'    => 4,
                'photo'     => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=640&q=80',
            ],
        ];

        $marqueEntities = [];
        foreach ($marques as $data) {
            $marque = new Marque();
            $marque->setNom($data['nom']);
            $marque->setAnneeCreation($data['anneeCreation']);
            $marque->setPays($data['pays']);
            $manager->persist($marque);
            $marqueEntities[] = $marque;
        }

        foreach ($vehicules as $data) {
            $vehicule = new Vehicule();
            $vehicule->setModele($data['modele']);
            $vehicule->setPrix((string) $data['prix']);
            $vehicule->setPuissance($data['puissance']);
            $vehicule->setAnnee($data['annee']);
            $vehicule->setPhoto($data['photo']);
            $vehicule->setMarque($marqueEntities[$data['marque']]);
            $manager->persist($vehicule);
        }

        $manager->flush();
    }
}
