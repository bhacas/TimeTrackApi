<?php

namespace App\Infrastructure\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

//#[AsEventListener(event: 'kernel.exception')]
class ExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $status = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500;

        $response = new JsonResponse([
            'error' => $exception->getMessage(),
        ], $status);

        $event->setResponse($response);
    }
}
