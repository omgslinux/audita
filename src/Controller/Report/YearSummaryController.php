<?php

namespace App\Controller\Report;

use App\Entity\BudgetYear;
use App\Repository\BudgetItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/summary/byyear', name: 'app_report_summary_byyear_')]
class YearSummaryController extends AbstractController
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
        $h1 = "Resumen $year";
        $title = "Comparación presupuesto inicial y liquidado $year";
        //$budgets = $bRepo->findByYear($budgetYear);
        $totalInit = $totalCurrent = $devPos = $devNeg = 0;
        //foreach ($budgets as $budget) {
        foreach ($bRepo->findByYear($budgetYear) as $budget) {
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $totalInit += $init;
            $totalCurrent += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
                $devPos += $deviation;
            } else {
                $devNeg += $deviation;
            }
        }
        $totals = [
            'totalInit' => $totalInit,
            'totalCurrent' => $totalCurrent,
            'devPos' => $devPos,
            'devNeg' => $devNeg,
        ];
        return $this->render('report/summary/year_summary.html.twig', [
            //'budgets' => $budgets,
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
        ]);
    }
}
