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

/**
 * @Route("/expense")
 */
class ExpenseController extends AbstractController
{
    private $calculService;

    public function __construct(MailerService $mailerService)
    {
        $this->mailerService = $mailerService;
    }

    /**
     * @Route("/", name="expense_index", methods={"GET"})
     */
    public function index(ExpenseRepository $expenseRepository, Request $request): Response
    {
        # Get the tricount ID from the request url
        $requestUri = explode('/', $request->getRequestUri());
        $tricountId = (int)$requestUri[2];

        return $this->render('expense/index.html.twig', [
            'expenses' => $expenseRepository->findBy(['tricount' => $tricountId]),
        ]);
    }

    /**
     * @Route("/new", name="expense_new", methods={"GET"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $expense = new Expense();
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # Get the tricount ID from the request url
            $requestUri = explode('/', $request->getRequestUri());
            $tricountId = (int)$requestUri[2];

            # Get the tricount
            $em = $entityManager->getRepository(Tricount::class);
            $tricount = $em->findOneById($tricountId);
            
            $expense->setTricount($tricount);
            $entityManager->persist($expense);
            $entityManager->flush();

            # Send email to participants
            $participants = $expense->getUserRefund()->getValues();
            $url = 'http://localhost:8000/tricount/'.$tricountId.'/expense/'.$expense->getId();

            foreach ($participants as $participant) {
                $this->mailerService->sendEmail($url, $participant->getEmail());
            }

            return $this->redirectToRoute('tricount_expenses', ['id' => $tricountId], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('expense/new.html.twig', [
            'expense' => $expense,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="expense_show", methods={"GET"})
     */
    public function show(Expense $expense): Response
    {
        return $this->render('expense/show.html.twig', [
            'expense' => $expense,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="expense_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Expense $expense, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExpenseType::class, $expense);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('expense_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('expense/edit.html.twig', [
            'expense' => $expense,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="expense_delete", methods={"POST"})
     */
    public function delete(Request $request, Expense $expense, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$expense->getId(), $request->request->get('_token'))) {
            $entityManager->remove($expense);
            $entityManager->flush();
        }

        return $this->redirectToRoute('expense_index', [], Response::HTTP_SEE_OTHER);
    }

    public function getTricountId($request)
    {
        return 'aah';
    }
}
