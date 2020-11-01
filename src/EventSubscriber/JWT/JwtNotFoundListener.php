<?php

declare(strict_types=1);

namespace App\EventSubscriber\JWT;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTNotFoundEvent;

class JwtNotFoundListener
{
    private RequestStack $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public function onJwtNotFound(JWTNotFoundEvent $event)
    {
        if ($this->request->getCurrentRequest()->cookies->get('REFRESH_TOKEN')) {
            $data = [
                'status'  => Response::HTTP_UNAUTHORIZED . ' Unauthorized',
                'message' => 'Expired token',
            ];
            $response = new JsonResponse($data, 401);
            
            return $event->setResponse($response);
        }

        $data = [
            'status'  => Response::HTTP_FORBIDDEN . ' Forbidden',
            'message' => 'Missing token',
        ];
        $response = new JsonResponse($data, 401);

        return $event->setResponse($response);
    }
}