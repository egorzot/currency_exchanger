<?php


namespace App\Controller\Conversation;


use App\Entity\BankAmount;
use App\Entity\Conversation;
use App\Entity\ConversationFactory;
use App\Service\ExchangeProvider;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\NoResultException;
use Money\Converter;
use Money\Currencies\ISOCurrencies;
use Money\Exception\UnresolvableCurrencyPairException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConversationExecuteController extends AbstractController
{
    /**
     * @Route("/conversion/{id}/execute",methods={"POST"})
     * @param $id
     * @return JsonResponse
     * @throws \Exception
     */
    public function execute($id): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        try {
            /** @var Conversation $conversation */
            $conversation = $em->getRepository(Conversation::class)->getActualConversation($id);
        }
        catch (NoResultException $e){
            return $this->json(['Transaction is not prepared or timed out'], 404);
        }

        $this->getDoctrine()->getManager()->getConnection()->beginTransaction();
        try {
            /** @var BankAmount $bankAmountToSummarize */
            $bankAmountToSummarize = $em->getRepository(BankAmount::class)->findOneBy(['currency' => $conversation->getFromAmount()->getCurrency()->getCode()]);
            /** @var BankAmount $bankAmountToSubtract */
            $bankAmountToSubtract = $em->getRepository(BankAmount::class)->findOneBy(['currency' => $conversation->getToAmount()->getCurrency()->getCode()]);

            $bankMoneySummarized  = $bankAmountToSummarize->getAmount()->add($conversation->getFromAmount());
            $bankMoneySubtracted = $bankAmountToSubtract->getAmount()->subtract($conversation->getToAmount());


            $bankAmountToSummarize->setAmount($bankMoneySummarized);
            $bankAmountToSubtract->setAmount($bankMoneySubtracted);

            $em->persist($bankAmountToSummarize);
            $em->persist($bankAmountToSubtract);

            $conversation->setIsExecuted(true);
            $em->persist($conversation);

            $em->flush();
            $em->getConnection()->commit();
        } catch (\Exception $e) {
            $em->getConnection()->rollBack();
            throw $e;
        }
        return $this->json([]);
    }

}
