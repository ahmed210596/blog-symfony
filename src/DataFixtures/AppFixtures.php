<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('en_US');
    
        
        for ($i = 2; $i <= 5; $i++) {
            $category = new Category();
            
            // Generate fake data
            $category->setTitle($faker->word(1));
            
    
            
    
            $manager->persist($category);

            for ($i = 2; $i <= 7; $i++) {
                $post = new Post();
                
                // Generate fake data
                $post->setTitle($faker->sentence(3));
                $post->setContent($faker->paragraph());
        
                $createdAt = $faker->dateTimeBetween('-3 months');
                $createdAtImmutable = \DateTimeImmutable::createFromMutable($createdAt);
                $post->setCreatedAt($createdAtImmutable);
                $post->setCategory($category);
               // $category->Addpost($post);
                $manager->persist($post);
            }
        }
    
        $manager->flush();
    }
}

