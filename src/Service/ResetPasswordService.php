<?php

namespace App\Service;

use App\Interfaces\User\AdvancedUserInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Model\ResetPasswordToken;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ResetPasswordService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var MailerService
     */
    private $mailerService;

    /**
     * @var ResetPasswordHelperInterface
     */
    private $resetPasswordHelper;

    /**
     * @var HttpFondationService
     */
    private $httpFondationService;

    /**
     * ResetPasswordService constructor.
     *
     * @param UserRepository               $userRepository
     * @param MailerService                $mailerService
     * @param ResetPasswordHelperInterface $resetPasswordHelper
     * @param HttpFondationService         $httpFondationService
     */
    public function __construct(
        UserRepository $userRepository,
        MailerService $mailerService,
        ResetPasswordHelperInterface $resetPasswordHelper,
        HttpFondationService $httpFondationService
    ) {
        $this->userRepository = $userRepository;
        $this->mailerService = $mailerService;
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->httpFondationService = $httpFondationService;
    }

    /**
     * @param string $emailFormData
     *
     * @return RedirectResponse
     */
    public function processSendingPasswordResetEmail(string $emailFormData): RedirectResponse
    {
        $user = $this->userRepository->findOneBy([
            'email' => $emailFormData,
        ]);

        // Marks that you are allowed to see the app_check_email page.
        $this->setCanCheckEmailInSession();

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRouteAppCheckEmail();
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     'There was a problem handling your password reset request - %s',
            //     $e->getReason()
            // ));

            return $this->redirectToRouteAppCheckEmail();
        }

        $email = $this->mailerService->generateWebMasterEmail(
            $user,
            'Your password reset request',
            'reset_password/email.html.twig',
            [
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ]
        );

        $this->mailerService->send($email);

        return $this->redirectToRouteAppCheckEmail();
    }

    private function setCanCheckEmailInSession(): void
    {
        $this->httpFondationService->getSession()->set('ResetPasswordCheckEmail', true);
    }

    /**
     * @return RedirectResponse
     */
    private function redirectToRouteAppCheckEmail(): RedirectResponse
    {
        return new RedirectResponse($this->httpFondationService->getRouter()->generate('app_check_email'));
    }

    public function getTokenLifetime(): int
    {
        return $this->resetPasswordHelper->getTokenLifetime();
    }

    public function removeResetRequest($token): void
    {
        $this->resetPasswordHelper->removeResetRequest($token);
    }

    /**
     * @param AdvancedUserInterface $user
     *
     * @return ResetPasswordToken
     *
     * @throws ResetPasswordExceptionInterface
     */
    public function generateResetToken(AdvancedUserInterface $user): ResetPasswordToken
    {
        return $this->resetPasswordHelper->generateResetToken($user);
    }

    /**
     * @param string $token
     *
     * @return object
     *
     * @throws ResetPasswordExceptionInterface
     */
    public function validateTokenAndFetchUser(string $token): object
    {
        return $this->resetPasswordHelper->validateTokenAndFetchUser($token);
    }
}
