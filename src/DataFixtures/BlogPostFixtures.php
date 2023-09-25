<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;

class BlogPostFixtures extends BaseFixture
{
    public function load(ObjectManager $manager)
    {
        $users = $manager->getRepository(User::class)->findAll();

        $randomUsers = $this->faker->randomElements($users, 5);

        foreach ($randomUsers as $user) {
            for ($i = 0; $i < rand(1, 5); $i++) {
                $blogPost = new BlogPost();
                $blogPost->setUser($user)
                    ->setTitle($this->faker->title())
                    ->setContent($this->faker->paragraph())
                    ->setStatus(1);
                $manager->persist($blogPost);
            }
        }

        $manager->flush();
    }
}
