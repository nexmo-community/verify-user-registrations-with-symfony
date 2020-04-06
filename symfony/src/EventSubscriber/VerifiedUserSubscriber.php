<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class VerifiedUserSubscriber implements EventSubscriberInterface
{
    /** @var RouterInterface */
    protected $router;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    /**
     * @param RouterInterface $router
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        RouterInterface $router,
        TokenStorageInterface $tokenStorage
    ) {
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelController(ControllerEvent $event)
    {
        if (!preg_match('/^\/profile/i', $event->getRequest()->getPathInfo())) {
            return;
        }

        if (null === $user = $this->tokenStorage->getToken()->getUser()) {
            return;
        }
        
        // Check whether the user is verified, if they are, allow them to continue to their destination.
        if ($user->getVerified()) {
            return;
        }

        $route = $this->router->generate('app_register_verify');
        $event->setController(function () use ($route) {
            return new RedirectResponse($route);
        });
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}
