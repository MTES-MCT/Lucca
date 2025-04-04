<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Mailer;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Part\{DataPart, File};

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

readonly class SummaryAdherentSubscriptionMailer
{
    public function __construct(
        private RequestStack    $requestStack,
        private MailerInterface $mailer,

        #[Autowire(param: 'kernel.project_dir')]
        private string          $project_dir,
    )
    {
    }

    public function sendSubscriptionToAdherent(Adherent $adherent, $password, bool $displayFlashMessage = true): bool
    {
        $email = new TemplatedEmail();

        /** Step I - Build parameter : from -> array(email => name) */
        $from = new Address(
            SettingManager::get('setting.general.emailGlobal.name'), SettingManager::get('setting.general.app.name')
        );

        /** Step II - Build parameter: to -> array(email => name) */
        $name = $adherent->getOfficialName();
        $to = new Address($adherent->getUser()->getEmail(), $name);

        /** Step III - Build parameter: cc -> array(email => name) */
        /** Step IV - Build parameter: bcc -> array(email => name) */

        /** Step V - Build parameter: subject -> string */
        $subject = '[' . SettingManager::get('setting.general.app.name') . '] [' .
            SettingManager::get('setting.general.ddtName.name') . '] Inscription de ' . $name;

        /** Step VI - Initialize message */
        $email
            ->from($from)
            ->to($to)
            ->subject($subject);

        /** Step VII - Build parameters: one/many attachments  */
        /** Create template mail */
        // get the image contents from an existing file
        $logoPath = $this->project_dir . '/public/assets/logo/lucca-logo-transparent.png';
        $email->addPart((new DataPart(new File($logoPath), 'logo'))->asInline());

        /** Step VIII - Build view  */
        // path of the Twig template to render
        $email->htmlTemplate('@LuccaAdherent/Mailer/subscription.html.twig');
        // pass variables (name => value) to the template
        $email->context([
            'adherent' => $adherent,
            'password' => $password,
            'web_url' => SettingManager::get('setting.general.url.name')
        ]);

        /** Step IX - Send email and display flash message if needed  */
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface) {
            if ($displayFlashMessage) {
                $this->requestStack->getSession()->getFlashBag()->add('danger', 'flash.mail.adherentSubscription.sendFail');
            }

            return false;
        }

        if ($displayFlashMessage) {
            $this->requestStack->getSession()->getFlashBag()->add('info', 'flash.mail.adherentSubscription.sendSuccessfully');
        }

        return true;
    }
}
