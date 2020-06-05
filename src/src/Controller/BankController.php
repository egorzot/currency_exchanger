<?php


namespace App\Controller;


use App\Entity\BankAmount;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class BankController extends AbstractController
{
    /**
     * @Route("/bank",methods={"GET"})
     * @return JsonResponse
     */
    public function bank()
    {
        $bankAmounts = $this->getDoctrine()->getManager()->getRepository(BankAmount::class)->findAll();
        $result = [];
        /** @var BankAmount $bankAmount */
        foreach ($bankAmounts as $bankAmount) {
            $result[] = [
                'currency' => $bankAmount->getAmount()->getCurrency(),
                'amount' => (string)($bankAmount->getAmount()->getAmount() / 100)
            ];
        }
        return $this->json($result);
    }
}