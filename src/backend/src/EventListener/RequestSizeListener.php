<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\JsonResponse;

class RequestSizeListener
{
    private int $maxSize;

    public function __construct(int $maxSize)
    {
        $this->maxSize = $maxSize;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $contentLength = $request->headers->get('Content-Length');

        if ($contentLength !== null && (int)$contentLength > $this->maxSize) {
            $event->setResponse(
                new JsonResponse(
                    ['error' => 'Request size too large'],
                    413
                )
            );
        }
    }
}
