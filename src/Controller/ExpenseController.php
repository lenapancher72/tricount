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
     * @Route("/tricount/{tricount_id}/expense", name="expense_index", methods={"GET"})
     */
    public function index(ExpenseRepository $expenseRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        # Get the tricount ID from the request url
        $requestUri = explode('/', $request->getRequestUri());
        $tricountId = (int)$requestUri[2];

        $em = $entityManager->getRepository(Tricount::class);
        $tricount = $em->findOneById($tricountId);

        $balance = $this->balanceService->makeBalance($tricountId);

        return $this->render('expense/index.html.twig', [
            'expenses' => $expenseRepository->findBy(['tricount' => $tricountId]),
            'tricount' => $tricountId,
            'device' => $tricount->getDevice(),
            'balance' => $balance
        ]);
    }

    /**
     * @Route("/tricount/{tricount_id}/expense/new", name="expense_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $expense = new Expense();
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        # Get the tricount's ID from the request url
        $requestUri = explode('/', $request->getRequestUri());
        $tricountId = (int)$requestUri[2];

        # Get the tricount
        $em = $entityManager->getRepository(Tricount::class);
        $tricount = $em->findOneById($tricountId);

        if ($form->isSubmitted() && $form->isValid()) {
            $expense->setTricount($tricount);
            $entityManager->persist($expense);
            $entityManager->flush();

            # Send email to participants
            $participants = $expense->getUserRefund()->getValues();
            $url = 'http://localhost:8000/tricount/'.$tricountId.'/expense/'.$expense->getId();

            foreach ($participants as $participant) {
                $this->mailerService->sendEmail($url, $participant->getEmail());
            }

            return $this->redirectToRoute('expense_index', ['tricount_id' => $tricountId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('expense/new.html.twig', [
            'expense' => $expense,
            'form' => $form,
            'tricount' => $tricountId
        ]);
    }

    /**
     * @Route("/tricount/{tricount_id}/expense/{expense_id}", name="expense_show", methods={"GET"})
     */
    public function show(ExpenseRepository $expenseRepository, Request $request, $expense_id): Response
    {
        # Get the tricount ID from the request url
        $requestUriTricountId = explode('/', $request->getRequestUri());
        $tricountId = (int)$requestUriTricountId[2];

        return $this->render('expense/show.html.twig', [
            'expense' => $expenseRepository->findOneById($expense_id),
            'tricount' => $tricountId,
        ]);
    }

    /**
     * @Route("/tricount/{tricount_id}/expense/edit", name="expense_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, ExpenseRepository $expenseRepository, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExpenseType::class, null);
        $form->handleRequest($request);

        # Get the tricount ID from the request url
        $requestUri = explode('/', $request->getRequestUri());
        $tricountId = (int)$requestUri[2];

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('expense_index', [
                'tricount_id' => $tricountId,
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('expense/edit.html.twig', [
            'expense' => $expenseRepository->findBy(['tricount' => $tricountId]),
            'form' => $form,
            'tricount' => $tricountId,
        ]);
    }

    /**
     * @Route("/expense/delete", name="expense_delete", methods={"POST"})
     */
    public function delete(Request $request, ExpenseRepository $expense, EntityManagerInterface $entityManager): Response
    {
        # Get the tricount ID from the request url
        $requestUriTricountId = explode('/', $request->getRequestUri());
        $tricountId = (int)$requestUriTricountId[2];

        if ($this->isCsrfTokenValid('delete' . $expense->getId(), $request->request->get('_token'))) {
            $entityManager->remove($expense);
            $entityManager->flush();
        }

        return $this->redirectToRoute('expense_index', [
            'tricount_id' => $tricountId,
        ], Response::HTTP_SEE_OTHER);
    }

    public function getTricountId($request)
    {
        return 'aah';
    }
}
