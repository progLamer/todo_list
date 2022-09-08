<?php
declare(strict_types=1);

namespace App\Tests\Api\Task;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class DeleteTest extends TaskTestCase
{
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testExists(): void
    {
        DeleteTest::createClient()->request('DELETE', '/api/tasks/1');

        $count = $this->taskRepository->count([]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($this->taskCount - 1, $count);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testNotExists(): void
    {
        DeleteTest::createClient()->request('DELETE', '/api/tasks/0');

        $count = $this->taskRepository->count([]);

        $this->assertResponseStatusCodeSame(404);
        $this->assertEquals($this->taskCount, $count);
    }
}
