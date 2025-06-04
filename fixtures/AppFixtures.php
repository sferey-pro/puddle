<?php

declare(strict_types=1);

namespace DataFixtures;

use App\Module\ProductCatalog\Tests\Story\ProductStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        ProductStory::load();
    }
}
