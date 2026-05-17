<?php

declare(strict_types=1);

namespace App\Shared\EventListener;

use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Forces no-cache headers on every response in the dev environment so the
 * browser always fetches the latest CSS/JS/HTML when the source changes —
 * no more hard refresh required after a translation tweak or a CSS edit.
 *
 * Only registered when APP_ENV=dev via #[When].
 */
#[When(env: 'dev')]
#[AsEventListener]
final class DisableHttpCacheInDev
{
    public function __invoke(ResponseEvent $event): void
    {
        $path = $event->getRequest()->getPathInfo();
        if (str_starts_with($path, '/_profiler') || str_starts_with($path, '/_wdt')) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
    }
}
