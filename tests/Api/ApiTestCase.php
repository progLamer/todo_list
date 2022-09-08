<?php
declare(strict_types=1);

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase as ApiTestCaseAlias;
use Doctrine\ORM\EntityManager;

abstract class ApiTestCase extends ApiTestCaseAlias
{
    protected ?EntityManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->entityManager = self::bootKernel()
            ->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}