<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Interfaces\User\AdvancedUserInterface;
use App\Service\ResetPasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;

/**
 * @Route("/reset-password")
 */
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    /**
     * @var string|null
     */
    private $token = null;

    /**
     * @var ResetPasswordService
     */
    private $resetPasswordService;

    public function __construct(ResetPasswordService $resetPasswordService)
    {
        $this->resetPasswordService = $resetPasswordService;
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("", name="app_forgot_password_request")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws TransportExceptionInterface
     */
    public function request(Request $request): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->resetPasswordService->processSendingPasswordResetEmail($form->get('email')->getData());
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/check-email", name="app_check_email")
     */
    public function checkEmail(): Response
    {
        // We prevent users from directly accessing this page
        if (!$this->canCheckEmail()) {
            return $this->redirectToRoute('app_forgot_password_request');
        }

        return $this->render('reset_password/check_email.html.twig', [
            'tokenLifetime' => $this->resetPasswordService->getTokenLifetime(),
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{token}", name="app_reset_password")
     *
     * @param Request                      $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param string|null                  $token
     *
     * @return Response
     */
    public function reset(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        string $token = null
    ): Response {
        $return = $this->getUserOrRedirectResponse($token);

        if ($return instanceof RedirectResponse) {
            return $return;
        }

        $user = $return;

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordService->removeResetRequest($this->token);

            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData());

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('index');
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    /**
     * @return string
     */
    private function getToken(): string
    {
        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        $this->token = $token;

        return $token;
    }

    /**
     * @param ResetPasswordExceptionInterface $exception
     *
     * @return RedirectResponse
     */
    private function addFlashMessageAndRedirect(ResetPasswordExceptionInterface $exception): RedirectResponse
    {
        $this->addFlash('reset_password_error', sprintf(
            'There was a problem validating your reset request - %s',
            $exception->getReason()
        ));

        return $this->redirectToRoute('app_forgot_password_request');
    }

    /**
     * @param string|null $token
     *
     * @return AdvancedUserInterface|RedirectResponse
     */
    private function getUserOrRedirectResponse(?string $token = null)
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }

        $token = $this->getToken();

        try {
            /** @var AdvancedUserInterface $user */
            $user = $this->resetPasswordService->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->addFlashMessageAndRedirect($e);
        }

        return $user;
    }
}
