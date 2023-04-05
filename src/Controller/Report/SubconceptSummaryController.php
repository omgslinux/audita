<?php

namespace App\Controller\Report;

use App\Entity\BudgetYear;
use App\Repository\BudgetChapterRepository;
use App\Repository\BudgetItemRepository;
use App\Repository\ProgrammRepository as PR;
use App\Repository\SubconceptRepository as SCR;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/summary/bysubconcept', name: 'app_report_summary_subconcept_detail_')]
class SubconceptSummaryController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['POST'])]
    public function postIndex(Request $request): Response
    {
        $budgets = $bRepo->findByYear($budgetYear);
        return $this->redirectToRoute(self::PREFIX . "show", ['id' => $budgetYear->getId()]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function index(BudgetItemRepository $bRepo, SCR $SCR, BudgetYear $budgetYear): Response
    {
        $year = $budgetYear->getYear()->format('Y');
        $h1 = "Presupuestos $year: Clasificación económica por subconcepto";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Concepto';
        $totals = [
            'caption' => [],
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        foreach ($SCR->findByYear($budgetYear, ['code'=>'ASC']) as $item) {
            $totals['caption'][$item->getCode()] = [
                'item' => $item,
                'totalInit' => 0,
                'totalCurrent' => 0,
                'devPos' => 0,
                'devNeg' => 0
            ];
        }

        $totalInit = $totalCurrent = $devPos = $devNeg = 0;
        foreach ($bRepo->findByYear($budgetYear) as $budget) {
            $subconcept = $budget->getSubconcept();
            $code = $subconcept->getCode();
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $totals['caption'][$code]['totalInit'] += $init;
            $totals['totalInit'] += $init;
            $totals['caption'][$code]['totalCurrent'] += $current;
            $totals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
                $totals['caption'][$code]['devPos'] += $deviation;
                $totals['devPos'] += $deviation;
            } else {
                $totals['caption'][$code]['devNeg'] += $deviation;
                $totals['devNeg'] += $deviation;
            }
        }//dump($totals);

        return $this->render('report/summary/subconcept_summary.html.twig', [
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
            'caption' => $caption,
        ]);
    }
}
