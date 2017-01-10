<?php

namespace AppBundle\Security\Provider;

use FOS\UserBundle\Security\UserProvider;

/**
 * Class EmailProvider.
 */
class EmailProvider extends UserProvider
{
    /**
     * @param string $username
     * 
     * @return \FOS\UserBundle\Model\UserInterface
     */
    protected function findUser($username)
    {
        return $this->userManager->findUserByEmail($username);
    }
}
