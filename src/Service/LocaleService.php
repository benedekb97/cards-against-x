<?php

declare(strict_types=1);

namespace App\Service;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\Country;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\LocaleSwitcher;

readonly class LocaleService implements LocaleServiceInterface
{
    public function __construct(
        private LocaleSwitcher $localeSwitcher,
        private ParameterBagInterface $parameterBag,
        private RequestStack $requestStack,
        private Reader $reader
    ) {}

    public function getCurrentLocaleCode(): string
    {
        return $this->localeSwitcher->getLocale();
    }

    public function getCurrentLocaleIcon(): string
    {
        return $this->getLocaleIcon($this->getCurrentLocaleCode());
    }

    public function getLocaleIcon(string $localeCode): string
    {
        return sprintf('<span class="fi fi-%s"></span>', self::LOCALE_COUNTRY_MAP[$localeCode]);
    }

    public function getAvailableLocales(): array
    {
        return $this->parameterBag->get('app.locales');
    }

    public function getLocaleName(string $localeCode): string
    {
        return self::LOCALE_NAME_MAP[$localeCode];
    }

    public function changeLocale(string $localeCode): void
    {
        $this->requestStack->getSession()->set(self::SESSION_LOCALE_KEY, $localeCode);
    }

    public function initializeLocale(): void
    {
        $this->getLocaleFromGeoIP();

        $locale = $this->requestStack->getSession()->get(self::SESSION_LOCALE_KEY) ?? $this->getLocaleFromGeoIP();

        if (null === $locale) {
            return;
        }

        $this->localeSwitcher->setLocale($locale);
    }

    private function getLocaleFromGeoIP(): ?string
    {
        try {
            $country = $this->reader->country($this->requestStack->getCurrentRequest()->getClientIp());

            /** @var array $locales */
            $locales = $country->locales;

            if (empty($locales)) {
                return null;
            }

            $locale = reset($locales);
        } catch (AddressNotFoundException|InvalidDatabaseException $e) {
            return null;
        }

        return $locale;
    }
}