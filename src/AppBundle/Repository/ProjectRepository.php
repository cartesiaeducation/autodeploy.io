<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;

/**
 * ProjectRepository.
 */
class ProjectRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function getByUser(User $user)
    {
        return $this->createQueryBuilder('p')
                    ->select('p')
                    ->leftJoin('p.collaborators', 'c')
                    ->where('p.user = :userId OR c.user = :userId')
                    ->setParameter('userId', $user->getId())
                    ->getQuery()
                    ->getResult();
    }
}
