<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Project;

/**
 * TaskRepository.
 */
class TaskRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param Project $project
     *
     * @return array
     */
    public function getByProject(Project $project)
    {
        return $this->createQueryBuilder('t')
                    ->select('t')
                    ->andWhere('t.project = :projectId')
                    ->andWhere('t.isEnabled = true')
                    ->setParameter('projectId', $project->getId())
                    ->orderBy('t.name', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @param Project $project
     * @param string  $name
     *
     * @return Task
     */
    public function getTaskByName(Project $project, $name)
    {
        return $this->createQueryBuilder('t')
                    ->select('t')
                    ->andWhere('t.project = :projectId')
                    ->andWhere('t.name = :name')
                    ->setParameter('projectId', $project->getId())
                    ->setParameter('name', $name)
                    ->getQuery()
                    ->getOneOrNullResult();
    }
}
