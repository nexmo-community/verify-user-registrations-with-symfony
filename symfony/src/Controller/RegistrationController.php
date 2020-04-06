<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Util\VonageUtil;
use App\Form\VerifyFormType;
use Nexmo\Verify\Verification;

class RegistrationController extends AbstractController
{
    /** @var VonageUtil */
    protected $vonageUtil;

    public function __construct(VonageUtil $vonageUtil)
    {
        $this->vonageUtil = $vonageUtil;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('profile');
        }

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setVerified(false);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            $verification = $this->vonageUtil->sendVerification($user);
            $requestId = $this->vonageUtil->getRequestId($verification);

            if ($requestId) {
                $user->setVerificationRequestId($requestId);
                $entityManager->flush();
            
                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/register/verify", name="app_register_verify")
     */
    public function verify(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(VerifyFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $verify = $this->vonageUtil->verify(
                $user->getVerificationRequestId(),
                $form->get('verificationCode')->getData()
            );

            if ($verify instanceof Verification) {
                $user->setVerificationRequestId(null);
                $user->setVerified(true);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                return $this->redirectToRoute('profile');
            }
        }

        return $this->render('registration/verify.html.twig', [
            'verificationForm' => $form->createView(),
        ]);
    }
}
