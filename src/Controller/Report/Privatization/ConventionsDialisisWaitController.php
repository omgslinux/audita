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

#[Route('/report/privatization/conventionsdialisiswait', name: 'app_report_privatization_conventions_dialisis_wait_')]
class ConventionsDialisisWaitController extends AbstractController
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
        $h1 = "Presupuestos $year: Convenios listas de espera diálisis";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Concepto';
        $this->report = $report;
        $this->report
        ->setYear($budgetYear)
        /*->setSubconcepts(
            [
            24700,
            25001,
            25101,
            25205,
            25301,
            25302,
            25704,
            25707,
            25802,
            ],
        )
        ->setProgrammes(['312C'], true) */
        ;
        //$totals = $report->getTotalsFromSub($budgetYear);
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
                    24700,
                    25001,
                    25101,
                    25205,
                    25301,
                    25302,
                    25704,
                    25707,
                    25802,
                ],
                'exclude' => false
            ],
            'progs' =>[
                'codes' =>
                [
                    '312C'
                ],
                'exclude' => true
            ],
        ];

        return $items;
    }


    public function calc(): array
    {
        return $this->report->getTotalsFromItems($this->getItems());
    }
}
