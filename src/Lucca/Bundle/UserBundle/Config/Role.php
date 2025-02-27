<?php

namespace Lucca\Bundle\UserBundle\Config;

use Symfony\Contracts\Translation\{TranslatableInterface, TranslatorInterface};

enum Role: string implements TranslatableInterface
{
    case ROLE_SUPER_ADMIN = 'choice.roles.superAdmin';
    case ROLE_ADMIN = 'choice.roles.admin';
    case ROLE_LUCCA = 'choice.roles.lucca';
    case ROLE_VISU = 'choice.roles.visu';
    case ROLE_FOLDER_OPEN = 'choice.roles.openFolder';
    case ROLE_DELETE_FOLDER = 'choice.roles.deleteFolder';

    public function trans(TranslatorInterface $translator, ?string $locale = null): string
    {
        return $translator->trans($this->value, domain: 'UserBundle', locale: $locale);
    }
}
