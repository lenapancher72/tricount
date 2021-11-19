<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\Tricount;
use App\Form\ExpenseType;
use App\Repository\ExpenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Services\MailerService;
use App\Services\BalanceService;
use Proxies\__CG__\App\Entity\Useraccount;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

/**
 * @Route("/")
 */
class ExpenseController extends AbstractController
{
    private $mailerService;
    private $balanceService;

    public function __construct(MailerService $mailerService, BalanceService $balanceService)
    {
        $this->mailerService = $mailerService;
        $this->balanceService = $balanceService;
    }

    /**
     * @Route("/tricount/{id}/expense", name="expense_index", methods={"GET"})
     */
    public function index(Tricount $tricount, Request $request, ExpenseRepository $expenseRepository): Response
    {
        $balance = $this->balanceService->makeBalance($tricount->getId());

        return $this->render('expense/index.html.twig', [
            'expenses' => $expenseRepository->findBy(['tricount' => $tricount]),
            'tricount' => $tricount->getId(),
            'device' => $tricount->getDevice(),
            'balance' => $balance
        ]);
    }

    /**
     * @Route("/tricount/{tricount_id}/expense/new", name="expense_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, $tricount_id): Response
    {
        $expense = new Expense();
        $newTricount = $entityManager->getRepository(Tricount::class)->find($tricount_id)->getParticipants();
        $form = $this->createForm(ExpenseType::class, $expense, ['tricount' => $newTricount]);
        $form->handleRequest($request);


        # Get the tricount's ID from the request url
        $requestUri = explode('/', $request->getRequestUri());
        $tricountId = (int)$requestUri[2];

        # Get the tricount
        $em = $entityManager->getRepository(Tricount::class);
        $tricount = $em->find($tricountId);

        if ($form->isSubmitted() && $form->isValid()) {
            $expense->setTricount($tricount);
            $entityManager->persist($expense);
            $entityManager->flush();

            # Send email to participants
            $participants = $expense->getUserRefund()->getValues();
            $url = 'http://localhost:8000/tricount/'.$tricountId.'/expense/show/'.$expense->getId();

            foreach ($participants as $participant) {
                $this->mailerService->sendEmail($url, $participant->getEmail());
            }

            return $this->redirectToRoute('expense_index', [
                'id' => $expense->getTricount()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('expense/new.html.twig', [
            'expense' => $expense,
            'form' => $form,
            'tricount' => $tricountId
        ]);
    }

    /**
     * @Route("/tricount/{tricount}/expense/show/{expense_id}", name="expense_show", methods={"GET"})
     */
    public function show($expense_id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $em = $entityManager->getRepository(Expense::class);
        $expense = $em->find($expense_id);

        return $this->render('expense/show.html.twig', [
            'expense' => $expense,
            'tricount' => $expense->getTricount(),
        ]);
    }

    /**
     * @Route("/tricount/{tricount}/expense/{id}/edit", name="expense_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, Expense $expense): Response
    {
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('expense_index', [
                'id' => $expense->getTricount()->getId(),
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('expense/edit.html.twig', [
            'expense' => $expense,
            'form' => $form,
            'tricount' => $expense->getTricount(),
        ]);
    }

    /**
     * @Route("/expense/{id}/delete", name="expense_delete", methods={"POST"})
     */
    public function delete(Request $request, Expense $expense, EntityManagerInterface $entityManager): Response
    {

        if ($this->isCsrfTokenValid('delete' . $expense->getId(), $request->request->get('_token'))) {
            $entityManager->remove($expense);
            $entityManager->flush();
        }

        return $this->redirectToRoute('expense_index', [
            'id' => $expense->getTricount()->getId(),
        ], Response::HTTP_SEE_OTHER);
    }

    public function getTricountId($request)
    {
        return 'aah';
    }
}
