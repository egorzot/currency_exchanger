<?php

namespace App\Repository;

use App\Entity\Conversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function getConversationsUidsForUser($user): array
    {
        $result = $this->getEntityManager()
            ->createQuery(
                'SELECT c.uuid
                    FROM App\Entity\Conversation c 
                    WHERE c.user = :user'
            )
            ->setParameter('user', $user)
            ->getScalarResult();

        if (empty($result) || !$result) {
            return [];
        }

        return array_column($result, 'uuid');
    }

    public function getActualConversation($uuid)
    {
        $qb = $this->createQueryBuilder('c');
        return $qb
            ->andWhere('c.uuid = :uuid')
            ->andWhere('c.expireAt > :now')
            ->andWhere($qb->expr()->eq('c.isExecuted', ':isExecuted'))
            ->setParameter(':uuid', $uuid)
            ->setParameter('now', new \DateTime(), Types::DATE_MUTABLE)
            ->setParameter('isExecuted', false)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
