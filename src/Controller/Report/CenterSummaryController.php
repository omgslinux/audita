<?php

namespace App\Controller\Report;

use App\Entity\BudgetYear;
use App\Repository\BudgetChapterRepository;
use App\Repository\BudgetItemRepository;
use App\Repository\ManagementCenterRepository as MCR;
use App\Repository\ProgrammRepository as PR;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/summary/bycenter', name: 'app_report_summary_by_center_')]
class CenterSummaryController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['POST'])]
    public function postIndex(Request $request): Response
    {
        $budgets = $bRepo->findByYear($budgetYear);
        return $this->redirectToRoute(self::PREFIX . "show", ['id' => $budgetYear->getId()]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function index(BudgetItemRepository $bRepo, MCR $MCR, BudgetYear $budgetYear): Response
    {
        $year = $budgetYear->getYear()->format('Y');
        $h1 = "Presupuestos $year: Clasificación económica por centros gestores";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Centro';
        $totals = [
            'chapter',
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        foreach ($MCR->findByYear($budgetYear, ['code'=>'ASC']) as $chapter) {
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
            $chapter = $budget->getCenter()->getCode();
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
        return $this->render('report/summary/center_summary.html.twig', [
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
            'caption' => $caption,
        ]);
    }
}
