<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\LocaleServiceInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: 'kernel.request', method: 'onKernelRequest')]
readonly class KernelEventListener
{
    public function __construct(
        private LocaleServiceInterface $localeService
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->localeService->initializeLocale();
    }
}