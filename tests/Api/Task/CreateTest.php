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

final class CreateTest extends TaskTestCase
{

    public function successDataProvider(): array
    {
        return [
            [' '],
            ['0'],
            ['test valid text for new task'],
            [str_repeat('g', 1000)],
        ];
    }

    public function failDataProvider(): array
    {
        return [
            ['', 'This value should not be blank.'],
            [str_repeat('t', 1001), 'This value is too long. It should have 1000 characters or less.'],
        ];
    }

    /**
     * @dataProvider successDataProvider
     *
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testSuccess($text)
    {
        $response = CreateTest::createClient()->request('POST', '/api/tasks', ['json' => ['text' => $text]]);

        $actual = $response->toArray();

        $this->assertArrayHasKey('id', $actual);

        $task = $this->taskRepository->find($actual['id']);
        $count = $this->taskRepository->count([]);

        $this->assertResponseIsSuccessful();
        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($this->taskCount + 1, $count);
        $this->assertJsonContains(
            [
                'id' => $task->getId(),
                'text' => $text,
                'status' => ['name' => TaskStatus::CREATED]
            ]
        );
    }

    /**
     * @dataProvider failDataProvider
     *
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testFail($text, $message)
    {
        CreateTest::createClient()->request('POST', '/api/tasks', ['json' => ['text' => $text]]);

        $count = $this->taskRepository->count([]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertEquals($this->taskCount, $count);
        $this->assertJsonContains(
            ['violations' => [['propertyPath' => 'text', 'message' => $message]]]
        );
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testFailWithoutText()
    {
        CreateTest::createClient()->request('POST', '/api/tasks', ['json' => []]);

        $count = $this->taskRepository->count([]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertEquals($this->taskCount, $count);
        $this->assertJsonContains(
            ['violations' => [['propertyPath' => 'text', 'message' => 'This value should not be blank.']]]
        );
    }
}
