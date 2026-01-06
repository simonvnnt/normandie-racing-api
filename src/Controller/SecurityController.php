<?php

namespace App\Controller;

use App\Dto\PasswordDto;
use App\Dto\RegisterDto;
use App\Dto\TokenDto;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route(path: '/api')]
class SecurityController extends AbstractController
{
    #[Route('/token_check', methods: ['POST'])]
    public function checkToken(
        #[MapRequestPayload] TokenDto $token,
        JWTEncoderInterface $JWTEncoder,
        JWTTokenManagerInterface $JWTTokenManager,
        UserRepository $userRepository,
        SerializerInterface $serializer,
    ): Response
    {
        try {
            $decodedToken = $JWTEncoder->decode($token->token);

            if ($decodedToken) {
                $user = $userRepository->findOneBy(['username' => $decodedToken['username']]);
                $token = $JWTTokenManager->create($user);

                $tokenData = [
                    'token' => $token,
                    'user' => $serializer->normalize($user, null, ['groups' => ["user"]])
                ];
                return $this->json($tokenData);
            }

            throw new JWTDecodeFailureException(JWTDecodeFailureException::INVALID_TOKEN, 'Invalid token');
        } catch (\Exception $e) {
            return $this->json(['message' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    #[Route('/register', methods: ['POST'])]
    public function register(
        #[MapRequestPayload] RegisterDto $registerDto,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response
    {
        $user = new User();
        $user->setUsername($registerDto->username);
        $passwordHashed = $passwordHasher->hashPassword($user, $registerDto->password);
        $user->setPassword($passwordHashed);

        $em->persist($user);
        $em->flush();

        return $this->json($user, Response::HTTP_CREATED);
    }

    #[Route('/{user}/change-password', methods: ['PUT'])]
    public function changePassword(
        User $user,
        #[MapRequestPayload] PasswordDto $passwordDto,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em
    ): Response
    {
        $passwordHashed = $passwordHasher->hashPassword($user, $passwordDto->password);
        $user->setPassword($passwordHashed);

        $em->persist($user);
        $em->flush();

        return $this->json($user, Response::HTTP_CREATED);
    }
}
