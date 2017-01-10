<?php

namespace AppBundle\Services\SSH;

use AppBundle\Entity\Authentification;
use Sinner\Phpseclib\Crypt\Crypt_RSA;

/**
 * Class SshManager.
 */
class SshManager
{
    /**
     * @param Authentification $authentification
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function checkAuthentification(Authentification $authentification)
    {
        return false;
    }

    /**
     * Generate new SSH RSA Key.
     *
     * @return array
     */
    public function generateKey()
    {
        define('CRYPT_RSA_COMMENT', 'autodeploy.io');
        $rsa = new Crypt_RSA();
        $rsa->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_OPENSSH);

        return $rsa->createKey(4096);
    }
}
