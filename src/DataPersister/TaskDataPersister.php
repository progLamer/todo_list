<?php
declare(strict_types=1);

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Task;
use App\Entity\TaskStatus;
use App\Repository\TaskRepository;
use App\Repository\TaskStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class TaskDataPersister implements ContextAwareDataPersisterInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Task;
    }

    /**
     * @inheritDoc
     * @return void
     * @throws NonUniqueResultException
     */
    public function persist($data, array $context = [])
    {
        $this->entityManager->clear();

        /** @var TaskRepository $taskRepository */
        $taskRepository = $this->entityManager->getRepository(Task::class);
        /** @var TaskStatusRepository $taskStatusRepository */
        $taskStatusRepository = $this->entityManager->getRepository(TaskStatus::class);

        $task = $data->getId() ? $taskRepository->find($data->getId()) : $data;

        $status = $data->getStatus();
        $statusName = $status ? $status->getName() : TaskStatus::CREATED;
        if ($status && TaskStatus::CREATED == $statusName) {
            throw new UnprocessableEntityHttpException('Status should be change from Created to Done');
        }
        $newTaskStatus = $taskStatusRepository->findOneByName($statusName);
        if (is_null($newTaskStatus)) {
            throw new NotFoundHttpException('Not Found');
        }
        $task->setStatus($newTaskStatus);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    /**
     * @inheritDoc
     */
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}