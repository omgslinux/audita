<?php

namespace App\Controller\Report;

use App\Entity\BudgetYear;
use App\Repository\BudgetChapterRepository;
use App\Repository\BudgetItemRepository;
use App\Repository\ProgrammRepository as PR;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/summary/byprogramm', name: 'app_report_summary_bychapter_programm_')]
class ProgrammSummaryController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['POST'])]
    public function postIndex(Request $request): Response
    {
        $budgets = $bRepo->findByYear($budgetYear);
        return $this->redirectToRoute(self::PREFIX . "show", ['id' => $budgetYear->getId()]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function index(BudgetItemRepository $bRepo, PR $PR, BudgetYear $budgetYear): Response
    {
        $year = $budgetYear->getYear()->format('Y');
        $h1 = "Presupuestos $year: Clasificación económica por programas";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Programa';
        $totals = [
            'chapter',
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        foreach ($PR->findByYear($budgetYear, ['code'=>'ASC']) as $chapter) {
            $totals['chapter'][$chapter->getCode()] = [
                'chapter' => $chapter,
                'totalInit' => 0,
                'totalCurrent' => 0,
                'devPos' => 0,
                'devNeg' => 0
            ];
        }

        $totalInit = $totalCurrent = $devPos = $devNeg = 0;
        foreach ($bRepo->findByYear($budgetYear) as $budget) {
            $chapter = $budget->getProgramm()->getCode();
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
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
            'caption' => $caption,
        ]);
    }
}
