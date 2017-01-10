<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Log;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class LogManager.
 */
class LogManager
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $managerRegistry;

    /**
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry->getManager();
    }

    /**
     * @param string $name
     * @param string $params
     *
     * @return Log
     */
    public function create($name, $params)
    {
        $log = new Log();
        $log->setConsumerParams($params);
        $log->setConsumerName($name);

        return $log;
    }

    /**
     * @param Log $log
     */
    public function save(Log $log)
    {
        if (null === $log->getId()) {
            $this->managerRegistry->persist($log);
        }
        $this->managerRegistry->flush();
    }
}
