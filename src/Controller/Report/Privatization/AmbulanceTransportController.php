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

#[Route('/report/privatization/ambulancetransport', name: 'app_report_privatization_ambulance_transport_')]
class AmbulanceTransportController extends AbstractController
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
        $h1 = "Presupuestos $year: Traslado ambulancias";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Concepto';
        $this->report = $report;
        //self::$report->setYear($budgetYear);
        $this->report->setYear($budgetYear);
        //$totals = $report->getTotalsFromSub($budgetYear);
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
                    25501,
                    25502,
                    25503,
                ],
                'exclude' => false
            ]
        ];

        return $items;
    }

    public function calc(): array
    {
        $items = $this->getItems();
        $this->report->setSubconcepts($items['subconcepts']['codes']);

        return $this->report->getTotalsFromSub();
    }
}
