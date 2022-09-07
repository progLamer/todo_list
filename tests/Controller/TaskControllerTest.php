<?php

namespace App\Tests\Controller;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TaskControllerTest extends ApiTestCase
{
    private ?EntityManager $entityManager;
    private ?TaskRepository $taskRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->taskRepository = $this->entityManager->getRepository(Task::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->taskRepository = null;
        $this->entityManager->close();
        $this->entityManager = null;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetCreatedTaskList(): void
    {
        $response = static::createClient()->request('GET', '/api/tasks');

        $this->assertResponseIsSuccessful();
//        $this->assertJsonContains(['@id' => '/']);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetDoneTaskList(): void
    {
        $response = static::createClient()->request('GET', '/api/tasks');

        $this->assertResponseIsSuccessful();
//        $this->assertJsonContains(['@id' => '/']);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testCreateTaskSuccess()
    {
        $response = static::createClient()->request('POST', '/api/tasks', ['json' => ['text' => 'test task1']]);

        $actual = $response->toArray();

        $this->assertArrayHasKey('data', $actual);
        $this->assertArrayHasKey('id', $actual['data']);

        $task = $this->taskRepository->find($actual['data']['id']);

        $this->assertResponseIsSuccessful();
        $this->assertInstanceOf(Task::class, $task);
        $this->assertJsonContains(['data' => ['id' => $task->getId()]]);
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testCreateTaskTooShort()
    {
        static::createClient()->request('POST', '/api/tasks', ['json' => ['text' => 'test']]);

        $count = $this->taskRepository->count([]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(2, $count);
        $this->assertJsonContains(
            ['errors' => ['text' => ['This value is too short. It should have 10 characters or more.']]]
        );
    }
}
