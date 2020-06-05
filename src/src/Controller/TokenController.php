<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class TokenController extends AbstractController
{
    private $jwtManager;

    /**
     * Token constructor.
     * @param JWTManager $jwtManager
     */
    public function __construct(JWTManager $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    /**
     * @Route("/token",methods={"POST"})
     * @return JsonResponse
     */
    public function execute(): JsonResponse
    {
        /** @var UserInterface $user */
        $user = $this->getDoctrine()->getManager()->find(User::class, rand(1, 10));

        return new JsonResponse(['token' => $this->jwtManager->create($user)]);
    }

}