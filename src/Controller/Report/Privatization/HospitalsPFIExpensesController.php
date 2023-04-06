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

#[Route('/report/privatization/hospitalspfiexpenses', name: 'app_report_privatization_hospitals_PFI_expenses_')]
class HospitalsPFIExpensesController extends AbstractController
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
        $h1 = "Presupuestos $year: Gastos hospitales modelo PFI";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Concepto';
        $report->setYear($budgetYear);
        //$report->setSubconcepts($search);
        //$totals = $report->getTotalsFromSub($budgetYear);
        $report->setHospitals('PFI');
        //$report->setCenters($report->getCodesByDescription($report->getHospitals()));
        $totals = $report->getTotalsFromCenter($budgetYear);

        return $this->render('report/summary/privatization_common.html.twig', [
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
            'caption' => $caption,
        ]);
    }
}
