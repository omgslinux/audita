<?php

namespace App\Controller\Report\Privatization;

use App\Entity\BudgetYear;
use App\Repository\BudgetChapterRepository;
use App\Repository\BudgetItemRepository;
use App\Repository\ProgrammRepository as PR;
use App\Repository\SubconceptRepository as SCR;
use App\Util\BudgetReport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/privatization/hospitalspficanon', name: 'app_report_privatization_hospitals_PFI_canon_')]
class HospitalsPFICanonController extends AbstractController
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
        $h1 = "Presupuestos $year: Canon hospitales modelo PFI";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Concepto';
        /*$report->setYear($budgetYear);
        $report->setSubconcepts(20201);
        //$totals = $report->getTotalsFromSub($budgetYear);
        $report->setHospitals('PFI');
        //$report->setCenters($report->getCodesByDescription($report->getHospitals()));
        $totals = $report->getTotalsFromCenter($budgetYear);*/
        $this->report = $report;
        $this->report->setYear($budgetYear);
        $totals = $this->calc();

        return $this->render('report/summary/privatization_common.html.twig', [
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
            'caption' => $caption,
            'items' => $this->getItems(),
      ]);
    }

    public static function getItems(): array
    {
        $items = [
            'subconcepts' =>[
                'codes' =>
                [
                    20201
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
            'centers' =>[
                'codes' =>
                [
                    //$this->getCodesByDescription($budgetYear, $hospitals['PPP'])
                ],
                'exclude' => false,
                'type' =>
                    //'NIVEL3',
                    'PFI',
                    //'PPP'
            ],
        ];

        return $items;
    }

    public function calc(): array
    {
        return $this->report->getTotalsFromItems($this->getItems());
    }
}
