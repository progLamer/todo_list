<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\TaskStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var TaskStatus $statusDone */
        $statusDone = $this->getReference(TaskStatus::DONE);
        /** @var TaskStatus $statusCreated */
        $statusCreated = $this->getReference(TaskStatus::CREATED);

        $tasks = [
            ['Task1 with status done active',$statusDone, false],
            ['Task2 with status done active',$statusDone, false],
            ['Task3 with status created active',$statusCreated, false],
            ['Task4 with status created active',$statusCreated, false],
            ['Task5 with status done deleted',$statusDone, true],
            ['Task6 with status created deleted',$statusCreated, true],
        ];

        $tasksForRemove = [];
        foreach ($tasks as [$text, $status, $isDeleted]) {
            $task = new Task();
            $task->setText($text);
            $task->setStatus($status);

            $manager->persist($task);

            if ($isDeleted) {
                $tasksForRemove[] = $task;
            }
        }

        $manager->flush();

        foreach ($tasksForRemove as &$removingTask) {
            $manager->remove($removingTask);
        }
        unset($removingTask);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [TaskStatusFixture::class];
    }
}
