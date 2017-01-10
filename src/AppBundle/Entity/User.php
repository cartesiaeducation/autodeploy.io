<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="app_user")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User extends BaseUser
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    protected $id;

    /**
     * @ORM\Column(name="locale", type="string", length=2)
     */
    protected $locale;

    /**
     * @ORM\Column(name="bitbucket_id", type="string", length=255, nullable=true)
     */
    protected $bitbucket_id;

    /**
     * @ORM\Column(name="bitbucket_access_token", type="string", length=255, nullable=true)
     */
    protected $bitbucket_access_token;

    /**
     * @ORM\Column(name="github_id", type="string", length=255, nullable=true)
     */
    protected $github_id;

    /**
     * @ORM\Column(name="github_access_token", type="string", length=255, nullable=true)
     */
    protected $github_access_token;

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->locale = 'en';
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles = [])
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }
    }

    /**
     * Set locale.
     *
     * @param string $locale
     *
     * @return User
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set bitbucketId.
     *
     * @param string $bitbucketId
     *
     * @return User
     */
    public function setBitbucketId($bitbucketId)
    {
        $this->bitbucket_id = $bitbucketId;

        return $this;
    }

    /**
     * Get bitbucketId.
     *
     * @return string
     */
    public function getBitbucketId()
    {
        return $this->bitbucket_id;
    }

    /**
     * Set bitbucketAccessToken.
     *
     * @param string $bitbucketAccessToken
     *
     * @return User
     */
    public function setBitbucketAccessToken($bitbucketAccessToken)
    {
        $this->bitbucket_access_token = $bitbucketAccessToken;

        return $this;
    }

    /**
     * Get bitbucketAccessToken.
     *
     * @return string
     */
    public function getBitbucketAccessToken()
    {
        return $this->bitbucket_access_token;
    }

    /**
     * Set githubId.
     *
     * @param string $githubId
     *
     * @return User
     */
    public function setGithubId($githubId)
    {
        $this->github_id = $githubId;

        return $this;
    }

    /**
     * Get githubId.
     *
     * @return string
     */
    public function getGithubId()
    {
        return $this->github_id;
    }

    /**
     * Set githubAccessToken.
     *
     * @param string $githubAccessToken
     *
     * @return User
     */
    public function setGithubAccessToken($githubAccessToken)
    {
        $this->github_access_token = $githubAccessToken;

        return $this;
    }

    /**
     * Get githubAccessToken.
     *
     * @return string
     */
    public function getGithubAccessToken()
    {
        return $this->github_access_token;
    }

    /**
     * @param $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }
}
