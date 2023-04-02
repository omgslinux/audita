<?php

namespace App\Controller\Admin;

use App\Entity\BudgetChapter;
use App\Form\BudgetChapterType;
use App\Repository\BudgetChapterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/budget/chapter', name: 'admin_budget_chapter_')]
class BudgetChapterController extends AbstractController
{
    const PREFIX = 'admin_budget_chapter_';

    public function __construct(BudgetChapterRepository $repo)
    {
        $this->repo = $repo;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('budget_chapter/index.html.twig', [
            'budget_chapters' => $this->repo->findAll(),
            'PREFIX' => self::PREFIX,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $budgetChapter = new BudgetChapter();
        $form = $this->createForm(BudgetChapterType::class, $budgetChapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->save($budgetChapter, true);

            return $this->redirectToRoute(self::PREFIX . 'index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('budget_chapter/new.html.twig', [
            'budget_chapter' => $budgetChapter,
            'form' => $form,
            'PREFIX' => self::PREFIX,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(BudgetChapter $budgetChapter): Response
    {
        return $this->render('budget_chapter/show.html.twig', [
            'budget_chapter' => $budgetChapter,
            'PREFIX' => self::PREFIX,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BudgetChapter $budgetChapter): Response
    {
        $form = $this->createForm(BudgetChapterType::class, $budgetChapter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->save($budgetChapter, true);

            return $this->redirectToRoute(self::PREFIX . 'index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('budget_chapter/edit.html.twig', [
            'budget_chapter' => $budgetChapter,
            'form' => $form,
            'PREFIX' => self::PREFIX,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, BudgetChapter $budgetChapter): Response
    {
        if ($this->isCsrfTokenValid('delete'.$budgetChapter->getId(), $request->request->get('_token'))) {
            $this->repo->remove($budgetChapter, true);
        }

        return $this->redirectToRoute(self::PREFIX . 'index', [], Response::HTTP_SEE_OTHER);
    }
}
