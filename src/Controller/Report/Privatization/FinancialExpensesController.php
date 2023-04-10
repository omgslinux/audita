<?php

namespace App\Controller\Report\Privatization;

use App\Entity\BudgetYear;
use App\Repository\BudgetChapterRepository;
use App\Repository\BudgetItemRepository;
use App\Repository\ProgrammRepository as PR;
use App\Repository\SubconceptRepository as SCR;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/privatization/financialexpenses', name: 'app_report_privatization_financial_expenses_')]
class FinancialExpensesController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['POST'])]
    public function postIndex(Request $request): Response
    {
        $budgets = $bRepo->findByYear($budgetYear);
        return $this->redirectToRoute(self::PREFIX . "show", ['id' => $budgetYear->getId()]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function show(ReportController $report, BudgetYear $budgetYear): Response
    {
        $year = $budgetYear->getYear()->format('Y');
        $h1 = "Presupuestos $year: Gasto financiero";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Concepto';
        /*$report->setYear($budgetYear);
        $report->setSubconcepts(
            [
            34200,
            35000,
            ],
        );
        $totals = $report->getTotalsFromSub($budgetYear);*/
        //$report->setHospitals('PPP');
        //$report->setCenters($report->getCodesByDescription($report->getHospitals()));
        //$totals = $report->getTotalsFromCenter($budgetYear);
        $this->report = $report;
        $this->report->setYear($budgetYear);
        $totals = $this->calc();

        return $this->render('report/summary/privatization_common.html.twig', [
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
            'caption' => $caption,
        ]);
    }
    public static function getItems(): array
    {
        $items = [
            'subconcepts' =>[
                'codes' =>
                [
                    34200,
                    35000,
                ],
                'exclude' => false
            ],
            'progs' =>[
                'codes' =>
                [
                    //'312C'
                ],
                'exclude' => false
            ],
        ];

        return $items;
    }

    public function calc(): array
    {
        $items = $this->getItems();
        $this->report->setSubconcepts($items['subconcepts']['codes']);
        if (!empty($items['progs']['codes'])) {
            $this->report->setProgrammes($items['progs']['codes'], $items['progs']['exclude']);
        }
        if (!empty($items['subconcepts']['codes'])) {
            $this->report->setSubconcepts($items['subconcepts']['codes'], $items['subconcepts']['exclude']);
            $t = $this->report->getTotalsFromSub();
        }
        if (!empty($items['centers']['codes'])) {
            $this->report->setCenters($items['centers']['codes'], $items['centers']['exclude']);
            $t = $this->report->getTotalsFromCenter();
        }

        return $t;
    }
}
