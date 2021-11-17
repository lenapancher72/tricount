<?php

namespace App\Controller;

use App\Entity\Tricount;
use App\Form\TricountType;
use App\Repository\TricountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tricount")
 */
class TricountController extends AbstractController
{
    /**
     * @Route("/", name="tricount_index", methods={"GET", "POST"})
     */
    public function index(TricountRepository $tricountRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $tricount = new Tricount();
        $form = $this->createForm(TricountType::class, $tricount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($tricount);
            $entityManager->flush();

            return $this->redirectToRoute('tricount_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('tricount/index.html.twig', [
            'tricounts' => $tricountRepository->findAll(),
            'tricount' => $tricount,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="tricount_show", methods={"GET"})
     */
    public function show(Tricount $tricount): Response
    {
        return $this->render('tricount/show.html.twig', [
            'tricount' => $tricount,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="tricount_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Tricount $tricount, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TricountType::class, $tricount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('tricount_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('tricount/edit.html.twig', [
            'tricount' => $tricount,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="tricount_delete", methods={"POST"})
     */
    public function delete(Request $request, Tricount $tricount, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tricount->getId(), $request->request->get('_token'))) {
            $entityManager->remove($tricount);
            $entityManager->flush();
        }

        return $this->redirectToRoute('tricount_index', [], Response::HTTP_SEE_OTHER);
    }
}
