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

#[Route('/report/privatization/surgeonmaterial', name: 'app_report_privatization_surgeon_material_')]
class SurgeonMaterialController extends AbstractController
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
        $h1 = "Presupuestos $year: Instrumental y material quirúrgico";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Concepto';
        /*$report->setYear($budgetYear);
        $report->setSubconcepts(
            [
            27001,
            27002,
            27004,
            27005,
            ],
        );
        $totals = $report->getTotalsFromSub($budgetYear); */
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
                    27001,
                    27002,
                    27004,
                    27005,
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
                    '',
                    //'PPP'
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
        if (!empty($items['centers'])) {
            if (!empty($items['centers']['codes'])) {
                $this->report->setCenters($items['centers']['codes'], $items['centers']['exclude']);
                $t = $this->report->getTotalsFromCenter();
            }
            if (!empty($items['centers']['type'])) {
                $this->report->setHospitals($items['centers']['type'], $items['centers']['exclude']);
                $t = $this->report->getTotalsFromCenter();
            }
        }

        return $t;
    }
}
