<?php


namespace App\Controller\Conversation;


use App\Entity\Conversation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\UsageTrackingTokenStorage;

class ConversationListController extends AbstractController
{
    private $tokenStorage;

    public function __construct(UsageTrackingTokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Route("/conversations",methods={"GET"})
     * @return JsonResponse
     */
    public function conversations()
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Conversation::class);
        $user = $this->tokenStorage->getToken()->getUser();
        return $this->json($repository->getConversationsUidsForUser($user));

    }

}
