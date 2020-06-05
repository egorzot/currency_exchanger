<?php

namespace App\DataFixtures;

use App\Entity\BankAmount;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Money\Money;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //users
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setRoles([]);
            $user->setUsername('user'.$i);
            $manager->persist($user);
        }

        //bank

        $bankAmountRub=new BankAmount();
        $bankAmountRub->setAmount(Money::RUB(1000000));
        $manager->persist($bankAmountRub);

        $bankAmountUsd=new BankAmount();
        $bankAmountUsd->setAmount(Money::USD(100000));
        $manager->persist($bankAmountUsd);

        $bankAmountEur=new BankAmount();
        $bankAmountEur->setAmount(Money::EUR(50000));
        $manager->persist($bankAmountEur);

        $manager->flush();
    }
}
