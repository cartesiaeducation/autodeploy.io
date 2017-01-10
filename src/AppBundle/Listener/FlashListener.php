<?php

namespace AppBundle\Listener;

use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Translation\TranslatorInterface;

class FlashListener implements EventSubscriberInterface
{
    private static $successMessages = [
        FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'change_password.flash.success',
        FOSUserEvents::GROUP_CREATE_COMPLETED    => 'group.flash.created',
        FOSUserEvents::GROUP_DELETE_COMPLETED    => 'group.flash.deleted',
        FOSUserEvents::GROUP_EDIT_COMPLETED      => 'group.flash.updated',
        FOSUserEvents::PROFILE_EDIT_COMPLETED    => 'profile.flash.updated',
        FOSUserEvents::RESETTING_RESET_COMPLETED => 'resetting.flash.success',
    ];

    private $session;
    private $translator;

    public function __construct(Session $session, TranslatorInterface $translator)
    {
        $this->session    = $session;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return [
            FOSUserEvents::CHANGE_PASSWORD_COMPLETED => 'addSuccessFlash',
            FOSUserEvents::GROUP_CREATE_COMPLETED    => 'addSuccessFlash',
            FOSUserEvents::GROUP_DELETE_COMPLETED    => 'addSuccessFlash',
            FOSUserEvents::GROUP_EDIT_COMPLETED      => 'addSuccessFlash',
            FOSUserEvents::PROFILE_EDIT_COMPLETED    => 'addSuccessFlash',
            FOSUserEvents::RESETTING_RESET_COMPLETED => 'addSuccessFlash',
        ];
    }

    public function addSuccessFlash(Event $event, $eventName = null)
    {
        // BC for SF < 2.4
        if (null === $eventName) {
            $eventName = $event->getName();
        }

        if (!isset(self::$successMessages[$eventName])) {
            throw new \InvalidArgumentException('This event does not correspond to a known flash message');
        }

        $this->session->getFlashBag()->add('success', $this->trans(self::$successMessages[$eventName]));
    }

    private function trans($message, array $params = [])
    {
        return $this->translator->trans($message, $params, 'FOSUserBundle');
    }
}
