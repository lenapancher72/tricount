<?php

namespace App\Services;

use App\Entity\Expense;
use App\Entity\Tricount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BalanceService extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function makeBalance(Int $tricountId)
    {

        $e = $this->entityManager->getRepository(Tricount::class);
        $tricount = $e->findOneById($tricountId);

        $em = $this->entityManager->getRepository(Expense::class);
        $expenses = $em->findBy(['tricount' => $tricountId]);

        $users = $this->getUserList($tricount->getParticipants()->getValues());

        foreach ($expenses as $expense) {
            $amount = $expense->getAmount();
            $userPaid = $expense->getUserPaid()->getId();
            $participants = $this->getParticipantList($expense->getUserRefund()->getValues());
            $amountPerUser = round($amount / count($participants), 2);
            $index = 0;

            foreach ($users as $user) {
                if ($this->isParticipant($user['id'], $participants)) {
                    if ($this->isUserPaidInParticipants($participants, $userPaid)) {
                        if ($user['id'] === $userPaid) {
                            $users[$index]['amount'] += $amount - $amountPerUser;
                        } else {
                            $users[$index]['amount'] -= $amountPerUser;
                        }
                    } else {
                        if (count($participants) === 1) {
                            $users[$index]['amount'] -= $amount;
                        } else {
                            $users[$index]['amount'] -= $amountPerUser;
                        }
                    }
                } else if ($user['id'] === $userPaid) {
                    $users[$index]['amount'] += $amount;
                }
                $index++;
            }
        }
        return $users;
    }


    public function getUserList($tricountUsers)
    {
        $users = [];

        foreach ($tricountUsers as $user) {
            $arr = [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'amount' => 0
            ];
            array_push($users, $arr);
        }
        return $users;
    }
    public function isParticipant($user, $participants)
    {
        foreach ($participants as $participantKey => $participantValue) {
            if ($user === $participantValue) {
                return true;
            }
        }
        return false;
    }
    public function getParticipantList($participants)
    {
        $participantArray = [];

        foreach ($participants as $participant) {
            array_push($participantArray, $participant->getId());
        }
        return $participantArray;
    }
    public function isUserPaidInParticipants($participants, $userPaid)
    {
        foreach ($participants as $participant) {
            if ($participant === $userPaid) {
                return true;
            }
        }
        return false;
    }
}