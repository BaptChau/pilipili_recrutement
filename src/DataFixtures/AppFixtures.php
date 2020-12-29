<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $brandName = ["Addidas","Nike","Puma","Carhartt"];

    public function load(ObjectManager $manager)
    {

        for ($i=0; $i < count($this->brandName) ; $i++) { 
            $product = new Brand();
            $product->setName($this->brandName[$i]);
            $manager->persist($product);

        }
        
        $manager->flush();
    }
}
