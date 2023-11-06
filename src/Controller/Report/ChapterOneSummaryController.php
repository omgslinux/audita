<?php

namespace App\Controller\Report;

use App\Entity\BudgetYear;
use App\Repository\BudgetChapterRepository;
use App\Repository\BudgetItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/summary/chapter1', name: 'app_report_chapter1_')]
class ChapterOneSummaryController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['POST'])]
    public function postIndex(Request $request): Response
    {
        $budgets = $bRepo->findByYear($budgetYear);
        return $this->redirectToRoute(self::PREFIX . "show", ['id' => $budgetYear->getId()]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function index(BudgetItemRepository $bRepo, BudgetYear $budgetYear): Response
    {
        $year = $budgetYear->getYear()->format('Y');
        $h1 = "Presupuestos $year: Capítulo 1: Gastos de personal";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $budgets = $bRepo->findByYearChapterNumber($budgetYear, 1);
        $totals = [
            'programm' => [],
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        $totalInit = $totalCurrent = $devPos = $devNeg = 0;
        foreach ($budgets as $budget) {
            //dump($budget);
            $budgetProgramm = $budget->getProgramm();
            $programm = $budgetProgramm->getCode();
            if (empty($totals['programm'][$programm])) {
                $totals['programm'][$programm] = [
                    'programm' => $budgetProgramm,
                    'totalInit' => 0,
                    'totalCurrent' => 0,
                    'devPos' => 0,
                    'devNeg' => 0
                ];
            }
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $totals['programm'][$programm]['totalInit'] += $init;
            $totals['totalInit'] += $init;
            $totals['programm'][$programm]['totalCurrent'] += $current;
            $totals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
                $totals['programm'][$programm]['devPos'] += $deviation;
                $totals['devPos'] += $deviation;
            } else {
                $totals['programm'][$programm]['devNeg'] += $deviation;
                $totals['devNeg'] += $deviation;
            }
        }//dump($budgets, $totals);
        $items = [
            [
                'title' => 'FORMACION POSTGRADO PERSONAL SANITARIO',
                'programm' => '312F'
            ],
            [
                'title' => 'RETRIBUCION PERSONAL PLAN INTEGRAL DE LISTAS DE ESPERA',
                'programm' => '312C'
            ],
            [
                'title' => 'RETRIBUCION PERSONAL ATENCION PRIMARIA',
                'programm' => '312B'
            ],
        ];

        return $this->render('report/summary/chapterone_summary.html.twig', [
            //'budgets' => $budgets,
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
            'items' => $items,
        ]);
    }
}
