<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use function array_key_exists;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use function get_class;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    const ARCHIVED = 'archived';
    const USER = 'u, r, l';
    const USER_ROLES = 'u.roles';
    const USER_LANGUAGE = 'u.language';

    /**
     * UserRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     *
     * @param UserInterface $user
     * @param string        $newEncodedPassword
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return User[]|null
     */
    public function findAllNotArchived(): ?array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.archived = :archived')
            ->setParameter('archived', false)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $uuid
     *
     * @return User|null
     *
     * @throws NonUniqueResultException
     */
    public function findOneByUuid(string $uuid): ?User
    {
        $query = $this->getQuery();

        $query = $query
            ->andWhere('u.uuid = :uuid')
            ->setParameter('uuid', $uuid)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param string $email
     *
     * @return User|null
     *
     * @throws NonUniqueResultException
     */
    public function findOneByEmail(string $email): ?User
    {
        $query = $this->getQuery();

        $query = $query
            ->andWhere('u.email = :email')
            ->setParameter('email', $email)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param array $options
     *
     * @return User[]
     */
    public function findAllInOneRequest(array $options = [])
    {
        $query = $this->getQuery();

        if (array_key_exists(self::ARCHIVED, $options)) {
            $archived = (bool) $options[self::ARCHIVED];

            $query = $query->andWhere('u.archived = :archived')
                ->setParameter(self::ARCHIVED, $archived);
        }

        $query = $query->getQuery();

        return $query->execute();
    }

    private function getQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('u')
            ->select(self::USER)
            ->leftJoin(self::USER_ROLES, 'r')
            ->leftJoin(self::USER_LANGUAGE, 'l');
    }
}
