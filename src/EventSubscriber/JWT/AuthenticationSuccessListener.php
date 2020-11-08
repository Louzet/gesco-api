<?php declare(strict_types=1);

namespace App\EventSubscriber\JWT;

use Symfony\Component\HttpFoundation\Cookie;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationSuccessResponse;

class AuthenticationSuccessListener
{
    private int $jwtTokenTTL;

    private JWTEncoderInterface $jwtEncoder;

    public function __construct(int $jwtTokenTTL, JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtTokenTTL = $jwtTokenTTL;
        $this->jwtEncoder = $jwtEncoder;
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        /** @var JWTAuthenticationSuccessResponse $response */
        $response = $event->getResponse();
        $data = $event->getData();

        $tokenJWT = $data['token'];

        $decodeToken = $this->jwtEncoder->decode($data['token']);

        $data['user'] = [
            'email' => $decodeToken['username'],
            'roles' => $decodeToken['roles'],
            'createdAt' => $decodeToken['createdAt']
        ];

        $event->setData($data);

        $response->headers->setCookie(Cookie::create('BEARER', $tokenJWT,mktime()+4200+$this->jwtTokenTTL ,'/', null, false, true));

        return $response;
    }
}