<?php


namespace App\Controller\Conversation;


use App\Entity\Conversation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ConversationInfoController extends AbstractController
{
    /**
     * @Route("/conversation/{id}",methods={"GET"})
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function info($id): JsonResponse
    {
        /** @var Conversation $conversation */
        $conversation = $this->getDoctrine()->getManager()->find(Conversation::class, $id);
        if (!$conversation) {
            return $this->json([], 404);
        }

        return $this->json([
            'isExecuted' => $conversation->getIsExecuted(),
            'fromAmount' => [
                'currency' => $conversation->getFromAmount()->getCurrency(),
                'amount' => (string)($conversation->getFromAmount()->getAmount() / 100)
            ],
            'resultAmount' => [
                'currency' => $conversation->getToAmount()->getCurrency(),
                'amount' => (string)($conversation->getToAmount()->getAmount() / 100)
            ],
            'rate' => $conversation->getRate(),
            'expiredAt' => $conversation->getExpireAt()->format('c')
        ]);
    }

}
