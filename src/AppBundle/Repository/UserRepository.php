<?php

namespace AppBundle\Repository;

/**
 * UserRepository.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param string $email
     *
     * @return User
     */
    public function getActiveByEmail($email)
    {
        return $this->createQueryBuilder('u')
                    ->select('u')
                    ->andWhere('u.email = :email')
                    ->setParameter('email', $email)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
