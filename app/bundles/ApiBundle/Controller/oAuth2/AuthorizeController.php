<?php

namespace Mautic\ApiBundle\Controller\oAuth2;

use FOS\OAuthServerBundle\Event\OAuthEvent;
use FOS\OAuthServerBundle\Form\Handler\AuthorizeFormHandler;
use FOS\OAuthServerBundle\Model\ClientManagerInterface;
use OAuth2\OAuth2;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthorizeController extends \FOS\OAuthServerBundle\Controller\AuthorizeController
{
    /**
     * This constructor must be duplicated from the extended class so our custom code could access the properties.
     */
    public function __construct(
        RequestStack $requestStack,
        private Form $authorizeForm,
        private AuthorizeFormHandler $authorizeFormHandler,
        private OAuth2 $oAuth2Server,
        private EngineInterface $templating,
        private TokenStorageInterface $tokenStorage,
        UrlGeneratorInterface $router,
        ClientManagerInterface $clientManager,
        private EventDispatcherInterface $eventDispatcher,
        private SessionInterface $session,
        $templateEngineType = 'php'
    ) {
        parent::__construct(
            $requestStack,
            $authorizeForm,
            $authorizeFormHandler,
            $oAuth2Server,
            $templating,
            $tokenStorage,
            $router,
            $clientManager,
            $eventDispatcher,
            $session,
            $templateEngineType
        );
    }

    /**
     *
     * @throws \OAuth2\OAuth2RedirectException
     * @throws AccessDeniedException
     */
    public function authorizeAction(Request $request): \FOS\OAuthServerBundle\Controller\Response|\Symfony\Component\HttpFoundation\Response
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        if (true === $this->session->get('_fos_oauth_server.ensure_logout')) {
            $this->session->invalidate(600);
            $this->session->set('_fos_oauth_server.ensure_logout', true);
        }

        $event = new OAuthEvent($user, $this->getClient());

        $this->eventDispatcher->dispatch(
            $event,
            OAuthEvent::PRE_AUTHORIZATION_PROCESS
        );

        if ($event->isAuthorizedClient()) {
            $scope = $request->get('scope', null);

            return $this->oAuth2Server->finishClientAuthorization(true, $user, $request, $scope);
        }

        if (true === $this->authorizeFormHandler->process()) {
            return $this->processSuccess($user, $this->authorizeFormHandler, $request);
        }

        return $this->templating->renderResponse(
            'MauticApiBundle:Authorize:oAuth2/authorize.html.php',
            [
                'form'   => $this->authorizeForm->createView(),
                'client' => $this->getClient(),
            ]
        );
    }
}
