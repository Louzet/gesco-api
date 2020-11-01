<?php declare(strict_types=1);

namespace App\Security;

use App\Entity\Agent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\CookieTokenExtractor;

class JwtCookieAuthenticator extends AbstractGuardAuthenticator
{
    public const LOGIN_ROUTE = "api_login_check";

    private JWTEncoderInterface $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder, EntityManagerInterface $em)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->em = $em;
    }

    /**
     * Return a response that redirect user to authenticate
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return Response
     */
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        if (\in_array('application/json', $request->getAcceptableContentTypes(), true) || 0 === mb_strpos($request->getPathInfo(), '/api')) {
            return new JsonResponse(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse(['message' => _('Auth token required')], Response::HTTP_FORBIDDEN);
    }

    /**
     * Does the authenticator support the given Request
     * 
     * if return false, the authenticator will be skipped
     *
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route') && $request->isMethod('POST');
        // return false !== $this->cookieTokenExtractor->extract($request);
    }

    /**
     * Get the authentication credentials from the request and return them
     * as any type (e.g: an associate array)
     * 
     * Whathever value you return here will be passed to getUser() and checkCredentials()
     *
     * @param Request $request
     * @return mixed
     */
    public function getCredentials(Request $request)
    {
        try {
            // $extractor = new AuthorizationHeaderTokenExtractor('Bearer ', 'Authorization');
            $extractor = new CookieTokenExtractor('BEARER');
            
            $token = $extractor->extract($request);
        } catch(\Exception $e) {
            throw new CustomUserMessageAuthenticationException('Failed to extract the Auth token.');
        }

        if (!$token) {
            throw new CustomUserMessageAuthenticationException('Authentication requires Auth Token.');
        }

        return $token;
    }

    /**
     * Return a UserInterface object based on the credentials
     * 
     * The credentials are the return value from getCredentials()
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return null|object|UserInterface
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->jwtEncoder->decode($credentials);
        } catch(\Exception | JWTDecodeFailureException $e) {
            throw new CustomUserMessageAuthenticationException('Token seems invalid, unverified or expired.');
        }

        $user = $this->em
            ->getRepository(Agent::class)
            ->findOneBy(['email' => $data['username']]);

        if (null === $user) {
            throw new CustomUserMessageAuthenticationException('Invalid credentials.');
        }

        return $user;
    }

    /**
     * Return true if the credentials are valid
     *
     * @param mixed $credentials
     * @param UserInterface $user
     * @return bool
     * 
     * @throws AuthenticationException
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * Called when authentication executed, but failed (e.g: wrong username or password)
     * 
     * This should return the Response sent back to the user, like a RedirectResponse
     * to the login page or a 403 response
     * 
     * If you return null, the request will continue, but the user will not be
     * authenticated. This is probably not what you want to do
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response|null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = ['message' => strtr($exception->getMessageKey(), $exception->getMessageData())];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication executed, and was successfull
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey): ?Response
    {
        return null;
    }

    /**
     * Does this method support remember_me cookies
     *
     * @return boolean
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }
}
