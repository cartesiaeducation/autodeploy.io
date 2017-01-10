<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use AppBundle\Entity\Webhook;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * Class WebhookManager.
 */
class WebhookManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Webhook $webhook
     */
    public function save(Webhook $webhook)
    {
        if (null === $webhook->getId()) {
            $this->generateToken($webhook);
            $this->em->persist($webhook);
        }

        $this->em->flush();
    }

    /**
     * @param Project $project
     *
     * @return array
     */
    public function getByProject(Project $project)
    {
        return $this->getRepository()->findByProject($project->getId());
    }

    /**
     * @return \AppBundle\Repository\WebhookRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('AppBundle:Webhook');
    }

    /**
     * @param Project $project
     * @param User    $user
     *
     * @return Webhook
     */
    public function create(Project $project, User $user)
    {
        $webhook = new Webhook();
        $webhook->setProject($project);
        $webhook->setUser($user);
        $webhook->setIsEnabled(true);

        return $webhook;
    }

    /**
     * @param string $token
     *
     * @return null|Webhook
     */
    public function getByToken($token)
    {
        return $this->getRepository()->getByToken($token);
    }

    /**
     * @param Webhook $webhook
     */
    private function generateToken(Webhook $webhook)
    {
        $generator = new SecureRandom();
        $webhook->setToken(sha1($generator->nextBytes(16)));
    }
}
