<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Afero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\AdherentBundle\Mailer;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;
use Twig\Error\Error;

use Lucca\Bundle\AdherentBundle\Entity\Adherent;
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

class SummaryAdherentSubscriptionMailer
{
    /**
     * Email automatic
     */
    private string $emailAuto = 'terence@numeric-wave.eu';

    /**
     * Name automatic
     */
    private string $nameAuto = 'Térence Gusse';

    public function __construct(
        private readonly ParameterBagInterface $params,
        private readonly RequestStack $requestStack,
        private readonly Environment $twig,
        private readonly MailerInterface $mailer,
    )
    {
    }

    public function sendSubscriptionToAdherent(Adherent $adherent, $password, bool $displayFlashMessage = true): ?bool
    {
        /** Step I - Build parameter : from -> array(email => name) */
        $from = [
            SettingManager::get('setting.general.emailGlobal.name') => SettingManager::get('setting.general.app.name')
        ];

        /** Step II - Build parameter: to -> array(email => name) */
        $name = $adherent->getOfficialName();
        $to = [$adherent->getUser()->getEmail() => $name];

        /** Step III - Build parameter: cc -> array(email => name) */
        /** Step IV - Build parameter: bcc -> array(email => name) */

        /** Step V - Build parameter: subject -> string */
        $subject = '[' . SettingManager::get('setting.general.app.name') . '] [' .
            SettingManager::get('setting.general.ddtName.name') . '] Inscription de ' . $name;

        /** Step VI - Initialize message swift */
        $swiftMail = \Swift_Message::newInstance()
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setBcc([
                $this->emailAuto => $this->nameAuto
            ]);

        /** Step VII - Build parameters: one/many attachments  */
        /** Create template mail */
        $logo = $swiftMail->embed(\Swift_Image::fromPath(
            $this->params->get('kernel.project_dir') . '/../web/assets/logo/lucca-logo-transparent.png'
        ));
        $web_url = SettingManager::get('setting.general.url.name');

        /** Step VIII - Build view  */
        $body = null;
        try {
            $body = $this->twig->render(
                '@LuccaAdherent/Mailer/subscription.html.twig',
                ['adherent' => $adherent, 'logo' => $logo, 'password' => $password, 'web_url' => $web_url]
            );
        } catch (Error $error) {
            echo 'Template generated for adherent subscription has fail - ' . $error->getMessage();
        }

        $swiftMail->setBody($body, 'text/html');

        /** Step IX - Send email and display flash message if needed  */
        $codeReturnEmail = $this->mailer->send($swiftMail);

        /** If return code is gt 0 - some emails has been sent */
        if ($displayFlashMessage === true && $codeReturnEmail > 0) {
            $this->requestStack->getSession()->getFlashBag()->add('info', 'flash.mail.adherentSubscription.sendSuccessfully');
        } elseif ($displayFlashMessage === true && $codeReturnEmail == 0) {
            $this->requestStack->getSession()->getFlashBag()->add('danger', 'flash.mail.adherentSubscription.sendFail');
        }

        return $codeReturnEmail;
    }

    public function getName(): string
    {
        return 'lucca.mailer.adherent.subscription.summary';
    }
}
