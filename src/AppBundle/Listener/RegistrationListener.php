<?php

namespace AppBundle\Listener;

use AppBundle\Services\Mailer;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class RegistrationListener.
 */
class RegistrationListener implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    protected $mailer;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param Mailer       $mailer
     * @param RequestStack $request
     */
    public function __construct(Mailer $mailer, RequestStack $request)
    {
        $this->mailer  = $mailer;
        $this->request = $request->getMasterRequest();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::REGISTRATION_INITIALIZE => [
                ['setUserLocale', 0],
            ],
        ];
    }

    /**
     * @param GetResponseUserEvent $event
     */
    public function setUserLocale(GetResponseUserEvent $event)
    {
        $event->getUser()->setLocale($this->request->getLocale());
    }
}
