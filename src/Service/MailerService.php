<?php

namespace App\Service;

use App\Interfaces\User\AdvancedUserInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerService
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * MailerService constructor.
     *
     * @param MailerInterface $mailer
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function generateWebMasterEmail(AdvancedUserInterface $user, string $subject, string $template, array $context)
    {
        return (new TemplatedEmail())
            ->from(new Address('webmaster@jouan.ovh', 'WebMaster'))
            ->to($user->getEmail())
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);
    }

    /**
     * @param $email
     *
     * @throws TransportExceptionInterface
     */
    public function send($email)
    {
        $this->mailer->send($email);
    }
}
