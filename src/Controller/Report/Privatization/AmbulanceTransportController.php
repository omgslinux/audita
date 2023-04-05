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

    private $BIR;
    private $SCR;
    private $PR;

    public function __construct(BudgetItemRepository $BIR, SCR $SCR, PR $PR)
    {
        $this->BIR = $BIR;
        $this->SCR = $SCR;
        $this->PR = $PR;
    }

    #[Route('/', name: 'index', methods: ['POST'])]
    public function postIndex(Request $request): Response
    {
        $budgets = $bRepo->findByYear($budgetYear);
        return $this->redirectToRoute(self::PREFIX . "show", ['id' => $budgetYear->getId()]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function index(BudgetYear $budgetYear): Response
    {
        $year = $budgetYear->getYear()->format('Y');
        $h1 = "Presupuestos $year: Traslado ambulancias";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Concepto';
        $totinit =
        [
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        $totals = $totinit
        ;
        // Busacmos los programas del año para los totales del año
        $progcodes = [];
        $exclude = false;
        if (!$exclude) {
            if (count($progcodes)) {
                $progs = $this->PR->findBy(['year' => $budgetYear, 'code' => $progcodes], ['code' => 'ASC']);
            } else {
                $progs = $this->PR->findBy(['year' => $budgetYear], ['code' => 'ASC']);
            }
        } else {
            $progs = $this->PR->findByYearExceptProgramms($budgetYear, $progcodes);
        }

        $progstotal =
        [
            'prog' => [],
            'total' => $totinit
        ];
        foreach ($progs as $item) {
            $progstotal['prog'][$item->getCode()] = [
                'item' => $item,
                'total' => $totinit
            ];
        }
        $captiontotal =
        [
            'caption' => [],
            'total' => $totinit
        ];
        $search =
        [
            25501,
            25502,
            25503,
        ];
        $subconcepts = $this->SCR->findBy(
            [
                'year' => $budgetYear,
                'code' => $search,
            ],
            ['code' => 'ASC']
        );
        foreach ($subconcepts as $item) {
            $captiontotal['caption'][$item->getCode()] = [
                'item' => $item,
                'total' => $totinit
            ];
        }

        $totalInit = $totalCurrent = $devPos = $devNeg = 0;
        foreach ($this->BIR->findBy(
            [
                    'year' => $budgetYear,
                    'programm' => $progs,
                    'subconcept' => $subconcepts
            ]
        ) as $budget) {
            $subconcept = $budget->getSubconcept();
            $code = $subconcept->getCode();
            $programm = $budget->getProgramm();
            $programmCode = $programm->getCode();
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $captiontotal['caption'][$code]['total']['totalInit'] += $init;
            $progstotal['prog'][$programmCode]['total']['totalInit'] += $init;
            $totals['totalInit'] += $init;
            $captiontotal['caption'][$code]['total']['totalCurrent'] += $current;
            $progstotal['prog'][$programmCode]['total']['totalCurrent'] += $current;
            $totals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
                $captiontotal['caption'][$code]['total']['devPos'] += $deviation;
                $progstotal['prog'][$programmCode]['total']['devPos'] += $deviation;
                $totals['devPos'] += $deviation;
            } else {
                $captiontotal['caption'][$code]['total']['devNeg'] += $deviation;
                $progstotal['prog'][$programmCode]['total']['devNeg'] += $deviation;
                $totals['devNeg'] += $deviation;
            }
        }//dump($captiontotal, $progstotal);
        $totals['caption'] = $captiontotal;
        $totals['prog'] = $progstotal;
        //dump($totals);

        return $this->render('report/summary/privatization_common.html.twig', [
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
            'caption' => $caption,
        ]);
    }
}
