<?php


namespace App\Controller\Conversation;


use App\Service\ConversationFactory;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Money\Exception\UnresolvableCurrencyPairException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConversationPrepareController extends AbstractController
{
    private $conversationFactory;

    public function __construct(ConversationFactory $conversationFactory)
    {
        $this->conversationFactory = $conversationFactory;
    }

    /**
     * @Route("/conversion/{id}/prepare",methods={"POST"})
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function prepare(Request $request, $id): JsonResponse
    {
        try {
            $conversation = $this->conversationFactory->createFromJson($request->getContent());
            $conversation->setUuid($id);
            $this->getDoctrine()->getManager()->persist($conversation);
            $this->getDoctrine()->getManager()->flush();
        } catch (UnresolvableCurrencyPairException $e) {
            return $this->json(['Invalid pair (no rate given in rates.json)'], 406);
        } catch (UniqueConstraintViolationException $e) {
            return $this->json(['Operation with this ID already exists'], 409);
        }

        $fromAmount = $conversation->getFromAmount();
        $resultAmount = $conversation->getToAmount();

        return $this->json([
            'fromAmount' => [
                'currency' => $fromAmount->getCurrency(),
                'amount' => (string)($fromAmount->getAmount() / 100)
            ],
            'resultAmount' => [
                'currency' => $resultAmount->getCurrency(),
                'amount' => (string)($resultAmount->getAmount() / 100)
            ],
            'rate' => $conversation->getRate(),
            'expiredAt' => $conversation->getExpireAt()->format('c')
        ]);
    }

}
