<?php

namespace AppBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{
    /**
     * {@inheritdoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();
        //on connect - get the access token and the user ID
        $service      = $response->getResourceOwner()->getName();
        $setter       = 'set' . ucfirst($service);
        $setter_id    = $setter . 'Id';
        $setter_token = $setter . 'AccessToken';
        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy([$property => $username])) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }
        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());
        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        if (!$response->getEmail()) {
            throw new AccountNotLinkedException(sprintf('Email not set.'));
        }

        $setter      = 'set' . ucfirst($response->getResourceOwner()->getName());
        $setterId    = $setter . 'Id';
        $setterToken = $setter . 'AccessToken';

        $user = $this->userManager->findUserByEmail($response->getEmail());

        if (null === $user) {
            // create new user here
            $user = $this->userManager->createUser();
            $user->setUsername($response->getNickname());
            $user->setEmail($response->getEmail());
            $user->setPassword(uniqid($response->getUsername()));
            $user->setEnabled(true);
        }

        $user->$setterId($response->getUsername());
        $user->$setterToken($response->getAccessToken());
        $this->userManager->updateUser($user);

        return $user;
    }
}
