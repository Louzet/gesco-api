<?php declare(strict_types=1);

namespace App\EventSubscriber\JWT;

use App\Entity\Agent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedListener
{
    public function updateJwtData(JWTCreatedEvent $event)
    {
        /** @var Agent $user */
        $user = $event->getUser();

        // 2. Enrichir les data pour qu'elles contiennent ces données
        $data = $event->getData();
        // $data['firstName'] = $user->getFirstName();
        // $data['lastName'] = $user->getLastName();
        $data['roles'] = $user->getRoles();
        $data['createdAt'] = $user->getCreatedAt();

        $event->setData($data);
    }
}