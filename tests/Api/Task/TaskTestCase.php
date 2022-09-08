<?php
declare(strict_types=1);

namespace App\Tests\Api\Task;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Tests\Api\ApiTestCase;

abstract class TaskTestCase extends ApiTestCase
{
    protected ?TaskRepository $taskRepository;
    protected int $taskCount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->taskRepository = $this->entityManager->getRepository(Task::class);
        $this->taskCount = $this->taskRepository->count([]);
    }

    protected function tearDown(): void
    {
        $this->taskRepository = null;

        parent::tearDown();
    }
}