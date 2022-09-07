<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TaskStatus;
use App\Repository\TaskRepository;
use App\Repository\TaskStatusRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskController extends AbstractController
{
    /**
     * @Route("/api/tasks", name="task_list", methods={"GET"})
     * @return JsonResponse
     */
    public function list(): JsonResponse
    {
        return $this->json([]);
    }

    /**
     * @Route("/api/tasks", name="task_create", methods={"POST"})
     * @param Request $request
     * @param TaskRepository $taskRepository
     * @param TaskStatusRepository $taskStatusRepository
     * @return JsonResponse
     * @throws NonUniqueResultException
     */
    public function create(
        Request $request,
        TaskRepository $taskRepository,
        TaskStatusRepository $taskStatusRepository,
        ValidatorInterface $validator
    ): JsonResponse {

        $statusCreated = $taskStatusRepository->findOneByName(TaskStatus::CREATED);

        $task = new Task();
        $task->setText($request->get('text'));
        $task->setStatus($statusCreated);

        $violations = $validator->validate($task);
        if ($violations->count()) {
            $errors = [];
            /** @var ConstraintViolation $violation */
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return $this->json(['errors' => $errors]);
        }

        $taskRepository->add($task, true);
        return $this->json(['data' => ['id' => $task->getId()]]);
    }

    /**
     * @Route("/api/tasks/{id<\d+>}", name="task_update", methods={"PATCH"})
     *
     * @param int $id
     * @return JsonResponse
     */
    public function update(int $id): JsonResponse
    {
        return $this->json([]);
    }

    /**
     * @Route("/api/tasks/{id<\d+>}", name="task_edit", methods={"PUT"})
     *
     * @param int $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        return $this->json([]);
    }

    /**
     * @Route("/api/tasks/{id<\d+>}", name="task_delete", methods={"DELETE"})
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        return $this->json([]);
    }
}