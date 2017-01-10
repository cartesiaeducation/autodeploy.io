<?php

namespace AppBundle\Repository;

/**
 * WebhookRepository.
 */
class WebhookRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param string $token
     *
     * @return Webhook
     */
    public function getByToken($token)
    {
        return $this->createQueryBuilder('w')
                    ->select('w')
                    ->innerJoin('w.project', 'p')
                    ->andWhere('w.token = :token')
                    ->andWhere('w.isEnabled = true')
                    ->andWhere('p.isEnabled = true')
                    ->setParameter('token', $token)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
