<?php
declare(strict_types=1);

namespace App\Tests\Api\Task;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

final class ListTest extends TaskTestCase
{
    /**
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function testGetCreated(): void
    {
        $status = 'Created';
        $response = ListTest::createClient()->request('GET', '/api/tasks?status.name=' . $status);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(
            [
                'hydra:member' => [
                    ['id' => 3, 'status' => ['name' => $status]],
                    ['id' => 4, 'status' => ['name' => $status]],
                ],
                'hydra:totalItems' => 2
            ]
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testGetDone(): void
    {
        $status = 'Done';
        ListTest::createClient()->request('GET', '/api/tasks?status.name=' . $status);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(            [
            'hydra:member' => [
                ['id' => 1, 'status' => ['name' => $status]],
                ['id' => 2, 'status' => ['name' => $status]],
            ],
            'hydra:totalItems' => 2
        ]);
    }
}
