<?php

declare(strict_types=1);

namespace App\Service;

interface LocaleServiceInterface
{
    public const LOCALE_COUNTRY_MAP = [
        'en' => 'gb',
        'hu' => 'hu',
    ];

    public const LOCALE_NAME_MAP = [
        'en' => 'English',
        'hu' => 'Magyar',
    ];

    public const SESSION_LOCALE_KEY = 'locale';

    public function getCurrentLocaleCode(): string;

    public function getCurrentLocaleIcon(): string;

    public function getLocaleIcon(string $localeCode): string;

    public function getAvailableLocales(): array;

    public function getLocaleName(string $localeCode): string;

    public function changeLocale(string $localeCode): void;

    public function initializeLocale(): void;
}