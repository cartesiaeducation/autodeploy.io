<?php

namespace AppBundle\Services;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Lexik\Bundle\MailerBundle\Message\MessageFactory;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class Mailer.
 */
class Mailer implements MailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var MessageFactory
     */
    protected $factory;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var string
     */
    protected $adminEmail;

    /**
     * @param \Swift_Mailer   $mailer
     * @param MessageFactory  $factory
     * @param RouterInterface $router
     * @param string          $adminEmail
     */
    public function __construct(\Swift_Mailer $mailer, MessageFactory $factory, RouterInterface $router, $adminEmail)
    {
        $this->mailer     = $mailer;
        $this->factory    = $factory;
        $this->router     = $router;
        $this->adminEmail = $adminEmail;
    }

    /**
     * @param \Swift_Message $message
     *
     * @return int
     */
    public function send(\Swift_Message $message)
    {
        return $this->mailer->send($message);
    }

    /**
     * @param UserInterface $user
     *
     * @return int
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $url     = $this->router->generate('fos_user_registration_confirm', ['token' => $user->getConfirmationToken()], true);
        $message = $this->factory->get('registration_confirm', $user->getEmail(), [
            'user'            => $user,
            'confirmationUrl' => $url,
        ], $user->getLocale());

        return $this->send($message);
    }

    /**
     * @param UserInterface $user
     *
     * @return int
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $url     = $this->router->generate('fos_user_resetting_reset', ['token' => $user->getConfirmationToken()], true);
        $message = $this->factory->get('registration_resetting', $user->getEmail(), [
            'user'         => $user,
            'resettingUrl' => $url,
        ], $user->getLocale());

        return $this->send($message);
    }

    /**
     * @param string $message
     * @param string $senderEmail
     * @param string $subject
     *
     * @return int
     */
    public function sendContactAdmin($message, $senderEmail, $subject)
    {
        $message = $this->factory->get('contact_admin', $this->adminEmail, [
            'senderEmail' => $senderEmail,
            'message'     => $message,
            'subject'     => $subject,
        ], 'fr');

        return $this->send($message);
    }

    /**
     * @param UserInterface $user
     *
     * @return int
     */
    public function sendNewCollaborator(UserInterface $user, Project $project, $password)
    {
        $url     = $this->router->generate('fos_user_registration_confirm', ['token' => $user->getConfirmationToken()], true);
        $message = $this->factory->get('create_collaborator', $user->getEmail(), [
            'user'            => $user,
            'project'         => $project,
            'password'        => $password,
            'confirmationUrl' => $url,
        ], $user->getLocale());

        return $this->send($message);
    }
}
