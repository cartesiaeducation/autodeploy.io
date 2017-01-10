<?php

namespace AppBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * UserToEmailTransformer.
 */
class UserToEmailTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($user)
    {
        if (null === $user) {
            return;
        }

        return $user->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($email)
    {
        if (empty($email)) {
            return;
        }

        $user = $this->em->getRepository('AppBundle:User')->getActiveByEmail($email);

        if (null === $user) {
            throw new TransformationFailedException(sprintf(
                'User with email %s does not exist',
                $email
            ));
        }

        return $user;
    }
}
