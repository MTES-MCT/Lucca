<?php
/*
 * Copyright (c) 2026. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */
namespace Lucca\Bundle\CoreBundle\Exception;

use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

class AigleNotificationException extends Exception
{
    private string $translationKey;
    private array $translationParams;

    private function __construct(string $translationKey, array $translationParams = [], int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($translationKey, $code, $previous);
        $this->translationKey = $translationKey;
        $this->translationParams = $translationParams;
    }

    public static function noParcelsFound(): self
    {
        return new self('text.aigleNotificationError.noParcelsFound');
    }

    public static function malFormedParcelString(string $parcel): self
    {
        return new self(
            'text.aigleNotificationError.malFormedParcelString',
            ['%parcel%' => $parcel]
        );
    }

    public static function errorDuringNotification(Exception $previous = null): self
    {
        return new self('text.aigleNotificationError.errorDuringNotification', [], 0, $previous);
    }

    public function getTranslationKey(): string
    {
        return $this->translationKey;
    }

    public function getTranslationParams(): array
    {
        return $this->translationParams;
    }

    public function getTranslatedMessage(?TranslatorInterface $translator = null): string
    {
        if ($translator) {
            return $translator->trans($this->translationKey, $this->translationParams, 'CoreBundle');
        }

        return $this->translationKey;
    }
}
