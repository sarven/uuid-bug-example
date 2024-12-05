<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Repository\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Entity;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\DBAL\Logging\SQLLogger;
use function array_column;
use function join;

final class EntityRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $entityRepository;
    private SQLLogger $sqlLogger;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->sqlLogger = new DebugStack();
        $this->entityRepository = self::getContainer()->get(EntityRepository::class);
    }

    public function testUpdate(): void
    {
        // Create
        $entity = new Entity('Name');
        $this->entityRepository->save($entity);
        $id = $entity->getId();
        $this->entityManager->clear();

        // Update without any changes -> no query should be executed
        $entity = $this->entityRepository->find($id);
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger($this->sqlLogger);
        $this->entityRepository->save($entity);

        self::assertEmpty($this->sqlLogger->queries, join("\n", array_column($this->sqlLogger->queries, 'sql')));
    }
}
