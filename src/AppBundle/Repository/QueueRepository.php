<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Environment;
use AppBundle\Entity\Project;

/**
 * QueueRepository.
 */
class QueueRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param Project $project
     *
     * @return bool
     */
    public function isQueueOccuped(Project $project, Environment $environment)
    {
        return (bool) $this->createQueryBuilder('q')
                            ->select('COUNT(q.id)')
                            ->andWhere('q.project = :projectId')
                            ->andWhere('q.environment = :envId')
                            ->andWhere('q.sendToQueue = true')
                            ->andWhere('q.isProcessed = false')
                            ->andWhere('q.createdAt >= :filterDate')
                            ->setParameter('projectId', $project->getId())
                            ->setParameter('filterDate', new \DateTime('-1 day'))
                            ->setParameter('envId', $environment->getId())
                            ->getQuery()
                            ->getSingleScalarResult();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function isTaskExisting(array $data)
    {
        return $this->createQueryBuilder('q')
                            ->select('q')
                            ->andWhere('q.project = :projectId')
                            ->andWhere('q.task = :taskId')
                            ->andWhere('q.environment = :envId')
                            ->andWhere('q.isProcessed = false')
                            ->andWhere('q.createdAt >= :filterDate')
                            ->setParameter('projectId', $data['project'])
                            ->setParameter('taskId', $data['task'])
                            ->setParameter('envId', $data['environment'])
                            ->setParameter('filterDate', new \DateTime('-1 day'))
                            ->getQuery()
                            ->getResult();
    }

    /**
     * Get history by project.
     *
     * @param Project $project
     *
     * @return array
     */
    public function getHistoryByProject(Project $project)
    {
        return $this->createQueryBuilder('q')
                    ->select('q')
                    ->andWhere('q.project = :projectId')
                    ->setParameter('projectId', $project->getId())
                    ->orderBy('q.createdAt', 'DESC')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return array
     */
    public function getToSendProcessSignal()
    {
        return $this->createQueryBuilder('q')
                    ->select('q')
                    ->andWhere('q.sendToQueue = false')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @param Project $project
     * @param int     $range
     *
     * @return array
     */
    public function getStatsByDays(Project $project, $range = 7)
    {
        return $this->createQueryBuilder('q')
                    ->select('COUNT(q) as number, q.state as label, DAY(q.createdAt) as day')
                    ->andWhere('q.project = :projectId')
                    ->setParameter('projectId', $project->getId())
                    ->andWhere('q.isProcessed = true')
                    ->andWhere('q.createdAt >= :filterRange')
                    ->setParameter('filterRange', new \DateTime(sprintf('-%d days', $range)))
                    ->groupBy('day, q.state')
                    ->getQuery()
                    ->getResult();
    }
}
