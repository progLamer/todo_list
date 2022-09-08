<?php
declare(strict_types=1);

namespace App\Tests\Api\Task;

use App\Entity\Task;
use App\Entity\TaskStatus;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class UpdateTest extends TaskTestCase
{
    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testSuccess()
    {
        $id = 3;
        $status = TaskStatus::DONE;
        CreateTest::createClient()
            ->request('PATCH', '/api/tasks/' . $id, [
                'json' => ['status' => ['name' => $status]],
                'headers' => ['content-type' => 'application/merge-patch+json']
            ]);

        $task = $this->taskRepository->find($id);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($status, $task->getStatus()->getName());
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['id' => $id, 'status' => ['name' => $status]]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function test404()
    {
        CreateTest::createClient()
            ->request('PATCH', '/api/tasks/0', [
                'json' => ['status' => ['name' => 'Done']],
                'headers' => ['content-type' => 'application/merge-patch+json']
            ]);

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testInvalidStatus()
    {
        $id = 3;
        $status = 'Invalid';
        CreateTest::createClient()
            ->request('PATCH', '/api/tasks/' . $id, [
                'json' => ['status' => ['name' => $status]],
                'headers' => ['content-type' => 'application/merge-patch+json']
            ]);

        $this->assertResponseStatusCodeSame(404);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testFailToCreated()
    {
        $id = 1;
        $status = TaskStatus::CREATED;
        CreateTest::createClient()
            ->request('PATCH', '/api/tasks/' . $id, [
                'json' => ['status' => ['name' => $status]],
                'headers' => ['content-type' => 'application/merge-patch+json']
            ]);

        $task = $this->taskRepository->find($id);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals(TaskStatus::DONE, $task->getStatus()->getName());
        $this->assertResponseStatusCodeSame(422);
    }
}