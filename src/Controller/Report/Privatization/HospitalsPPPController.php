<?php

namespace App\Controller\Report\Privatization;

use App\Entity\BudgetYear;
use App\Repository\BudgetChapterRepository;
use App\Repository\BudgetItemRepository;
use App\Repository\ManagementCenterRepository as MCR;
use App\Repository\ProgrammRepository as PR;
use App\Repository\SubconceptRepository as SCR;
use App\Util\BudgetReport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/privatization/hospitalsppp', name: 'app_report_privatization_PPP_model_hospitals_')]
class HospitalsPPPController extends AbstractController
{
    private $report;

    #[Route('/', name: 'index', methods: ['POST'])]
    public function postIndex(Request $request): Response
    {
        $budgets = $bRepo->findByYear($budgetYear);
        return $this->redirectToRoute(self::PREFIX . "show", ['id' => $budgetYear->getId()]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function show(BudgetReport $report, BudgetYear $budgetYear): Response
    {
        $year = $budgetYear->getYear()->format('Y');
        $h1 = "Presupuestos $year: Hospitales modelo PPP";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Concepto';
        $this->report = $report;
        //self::$report->setYear($budgetYear);
        $this->report->setYear($budgetYear);
        //$totals = $report->getTotalsFromSub();
        $totals = $this->calc();
        //$report->setHospitals('PPP');
        //$report->setCenters($report->getCodesByDescription($report->getHospitals()));
        //$totals = $report->getTotalsFromCenter($budgetYear);

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
                    25200,
                    25206,
                    25208,
                    25210,
                ],
            'exclude' => false
            ]
        ];

        return $items;
    }

    public function calc(): array
    {
        return $this->report->getTotalsFromItems($this->getItems());
    }
}
