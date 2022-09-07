<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\TaskStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaskStatusFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach ([TaskStatus::CREATED, TaskStatus::DONE] as $statusName) {
            $taskStatus = new TaskStatus();
            $taskStatus->setName($statusName);
            $manager->persist($taskStatus);

            $this->addReference($statusName, $taskStatus);
        }

        $manager->flush();
    }
}
