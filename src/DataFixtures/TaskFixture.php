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

        $task = new Task();
        $task->setText('Сформировать стек выполнения тестового задания');
        $task->setStatus($statusDone);
        $manager->persist($task);

        $task = new Task();
        $task->setText('Сделать тестовое задание');
        $task->setStatus($statusCreated);
        $manager->persist($task);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [TaskStatusFixture::class];
    }
}
