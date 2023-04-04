<?php

namespace App\Controller\Report;

use App\Entity\BudgetYear;
use App\Repository\BudgetChapterRepository;
use App\Repository\BudgetItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/summary', name: 'app_report_summary_bychapter_')]
class ChapterSummaryController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['POST'])]
    public function postIndex(Request $request): Response
    {
        $budgets = $bRepo->findByYear($budgetYear);
        return $this->redirectToRoute(self::PREFIX . "show", ['id' => $budgetYear->getId()]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function index(BudgetItemRepository $bRepo, BudgetChapterRepository $cRepo, BudgetYear $budgetYear): Response
    {
        $year = $budgetYear->getYear()->format('Y');
        $h1 = "Presupuestos $year: Clasificación económica por capítulos";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $totals = [
            'chapter',
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        foreach ($cRepo->findAll() as $chapter) {
            if ($chapter->getCode()<10) {
                $totals['chapter'][$chapter->getCode()] = [
                    'chapter' => $chapter,
                    'totalInit' => 0,
                    'totalCurrent' => 0,
                    'devPos' => 0,
                    'devNeg' => 0
                ];
            }
        }
        //$budgets = $bRepo->findByYear($budgetYear);
        $totalInit = $totalCurrent = $devPos = $devNeg = 0;
        //foreach ($budgets as $budget) {
        foreach ($bRepo->findByYear($budgetYear) as $budget) {
            $chapter = $budget->getSubconcept()->getChapter();
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $totals['chapter'][$chapter]['totalInit'] += $init;
            $totals['totalInit'] += $init;
            $totals['chapter'][$chapter]['totalCurrent'] += $current;
            $totals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
                $totals['chapter'][$chapter]['devPos'] += $deviation;
                $totals['devPos'] += $deviation;
            } else {
                $totals['chapter'][$chapter]['devNeg'] += $deviation;
                $totals['devNeg'] += $deviation;
            }
        }
        return $this->render('report/summary/chapter_summary.html.twig', [
            //'budgets' => $budgets,
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
        ]);
    }
}
