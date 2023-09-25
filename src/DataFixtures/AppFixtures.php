<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
    }

    public function getDependencies(): array
    {
        return [
            BlogPostFixtures::class,
            UserFixtures::class,
        ];
    }
}
