<?php

namespace App\Controller\Report\Privatization;

use App\Entity\BudgetYear;
use App\Repository\BudgetChapterRepository;
use App\Repository\BudgetItemRepository;
use App\Repository\ManagementCenterRepository as MCR;
use App\Repository\ProgrammRepository as PR;
use App\Repository\SubconceptRepository as SCR;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/privatization/hospitalsppp', name: 'app_report_privatization_PPP_model_hospitals_')]
class HospitalsPPPController extends AbstractController
{

    private $BIR;
    private $MCR;
    private $SCR;
    private $PR;
    private $captiontotal;
    private $progstotal;
    private $totinit =
        [
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];

    public function __construct(BudgetItemRepository $BIR, MCR $MCR, SCR $SCR, PR $PR)
    {
        $this->BIR = $BIR;
        $this->MCR = $MCR;
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
    public function index(BudgetYear $budgetYear, MCR $MCR): Response
    {
        $year = $budgetYear->getYear()->format('Y');
        $h1 = "Presupuestos $year: Hospitales modelo PPP";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Concepto';
        $hospitals = [
            'NIVEL3' => [
                'HOSPITAL UNIVERSITARIO LA PAZ',
                'HOSPITAL UNIVERSITARIO 12 DE OCTUBRE',
                'HOSPITAL UNIVERSITARIO RAMÓN Y CAJAL',
                'HOSPITAL CLÍNICO SAN CARLOS',
                'HOSPITAL UNIVERSITARIO DE LA PRINCESA',
                'HOSPITAL GENERAL UNIV. GREGORIO MARAÑÓN',
            ],
            'PFI' => [
                'HOSPITAL UNIVERSITARIO PUERTA HIERRO',
                'HOSPITAL UNIVERSITARIO DEL HENARES',
                'HOSPITAL UNIVERSITARIO DEL SURESTE',
                'HOSPITAL UNIVERSITARIO DEL TAJO',
                'HOSPITAL UNIVERSITARIO INFANTA CRISTINA',
                'HOSPITAL UNIVERSITARIO INFANTA LEONOR',
                'HOSPITAL UNIVERSITARIO INFANTA SOFÍA'
            ],
            'PPP' => [
                'NUEVO HOSPITAL DE MÓSTOLES',
                'HOSPITAL DE VALDEMORO',
                'HOSPITAL DE TORREJÓN',
                'HOSPITAL DE COLLADO VILLALBA'
            ]
        ];

        // Buscamos los codigos de todos los objetos para los totales del año
        $search =
        [
            'subconcepts' =>
            [
                'codes' =>
                [
                    25200,
                    25206,
                    25208,
                    25210,
                ],
                'exclude' => false,
            ],
            'progs' =>
            [
                'codes' => [

                ],
                'exclude' => false,
            ],
            'centers' =>
            [
                'codes' =>
                [
                    $this->getCodesByDescription($budgetYear, $hospitals['PPP'])
                ],
                'exclude' => false,
            ],

        ];

        $progs = $this->getProgs($budgetYear, $search['progs']);

        $this->captiontotal =
        [
            'caption' => [],
            'total' => $this->totinit
        ];
        /*$subconcepts = $this->SCR->findBy(
            [
                'year' => $budgetYear,
                'code' => $search,
            ],
            ['code' => 'ASC']
        ); */
        $subconcepts = $this->getSubconcepts($budgetYear, $search['subconcepts']);
        foreach ($subconcepts as $item) {
            $this->captiontotal['caption'][$item->getCode()] = [
                'item' => $item,
                'total' => $this->totinit
            ];
        }

        $totals = $this->getTotalsFromSub($budgetYear, $subconcepts, $progs);

        $totals['caption'] = $this->captiontotal;
        $totals['prog'] = $this->progstotal;
        //dump($totals);

        return $this->render('report/summary/privatization_common.html.twig', [
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
            'caption' => $caption,
        ]);
    }

    private function getItems(BudgetYear $year, $search, $repo): array
    {
        $codes = $search['codes'];
        if (!$search['exclude']) {
            if (count($codes)) {
                $items = $repo->findBy(['year' => $year, 'code' => $codes], ['code' => 'ASC']);
            } else {
                $items = $repo->findBy(['year' => $year], ['code' => 'ASC']);
            }
        } else {
            $items = $repo->findByYearExcept($year, $codes);
        }

        return $items;
    }

    private function getCodesByDescription(BudgetYear $year, $search): array
    {
        $codes = [];
        $items = $this->MCR->findByYear($year, ['code' => 'ASC']);
        foreach ($items as $item) {
            if (array_search($item->getDescription(), $search)) {
                $codes[] = $item->getCode();
            }
        }
        return $codes;
    }

    private function getCenters(BudgetYear $year, $search): array
    {
        return $this->getItems($year, $search, $this->MCR);
    }

    private function getProgs(BudgetYear $year, $search): array
    {
        $items = $this->getItems($year, $search, $this->PR);
        $this->progstotal =
        [
            'prog' => [],
            'total' => $this->totinit
        ];
        foreach ($items as $item) {
            $this->progstotal['prog'][$item->getCode()] = [
                'item' => $item,
                'total' => $this->totinit
            ];
        }

        return $items;
    }

    private function getSubconcepts(BudgetYear $year, $search): array
    {
        $items = $this->getItems($year, $search, $this->SCR);
        foreach ($items as $item) {
            $this->captiontotal['caption'][$item->getCode()] = [
                'item' => $item,
                'total' => $this->totinit
            ];
        }
        return $items;
    }

    private function getTotalsFromSub(BudgetYear $year, $subconcepts = null, $progs = null): array
    {
        $totals = $this->totinit;
        $totalInit = $totalCurrent = $devPos = $devNeg = 0;
        foreach ($this->BIR->findBy(
            [
                'year' => $year,
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
            $this->captiontotal['caption'][$code]['total']['totalInit'] += $init;
            $this->progstotal['prog'][$programmCode]['total']['totalInit'] += $init;
            $totals['totalInit'] += $init;
            $this->captiontotal['caption'][$code]['total']['totalCurrent'] += $current;
            $this->progstotal['prog'][$programmCode]['total']['totalCurrent'] += $current;
            $totals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
                $this->captiontotal['caption'][$code]['total']['devPos'] += $deviation;
                $this->progstotal['prog'][$programmCode]['total']['devPos'] += $deviation;
                $totals['devPos'] += $deviation;
            } else {
                $this->captiontotal['caption'][$code]['total']['devNeg'] += $deviation;
                $this->progstotal['prog'][$programmCode]['total']['devNeg'] += $deviation;
                $totals['devNeg'] += $deviation;
            }
        }//dump($captiontotal, $progstotal);

        return $totals;
    }

    private function getTotalsFromCenter()
    {

    }
}
