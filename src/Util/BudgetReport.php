<?php

namespace App\Util;

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

class BudgetReport extends AbstractController
{

    private $BIR;
    private $MCR;
    private $SCR;
    private $PR;
    private $captiontotal;
    private $progstotal;
    private $hospitals;
    private $totinit =
        [
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
    private $year=null;
    const SEARCH =
        [
            'subconcepts' =>
            [
                'codes' =>
                [
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
                    //$this->getCodesByDescription($budgetYear, $hospitals['PPP'])
                ],
                'exclude' => false,
            ],

        ];

    const HOSPITALS = [
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
    private $search = self::SEARCH;


    public function __construct(BudgetItemRepository $BIR, MCR $MCR, SCR $SCR, PR $PR)
    {
        $this->BIR = $BIR;
        $this->MCR = $MCR;
        $this->SCR = $SCR;
        $this->PR = $PR;
    }

    public function index(BudgetYear $budgetYear, MCR $MCR): Response
    {
        $year = $budgetYear->getYear()->format('Y');
        $h1 = "Presupuestos $year: Hospitales modelo PPP";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Concepto';

        // Buscamos los codigos de todos los objetos para los totales del año

        $progs = $this->getProgs($budgetYear, $search['progs']);

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

        return $this->render('report/summary/privatization_common.html.twig', [
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
            'caption' => $caption,
        ]);
    }

    public function initSearch()
    {
        $this->search = self::SEARCH;
    }

    private function getCenters($search, BudgetYear $year = null): array
    {
        $year = (null!=$year?$year:$this->year);

        $items = $this->getItems($search, $this->MCR, $year);
        $this->captiontotal =
        [
            'caption' => [],
            'total' => $this->totinit
        ];
        foreach ($items as $item) {
            $this->captiontotal['caption'][$item->getCode()] = [
                'item' => $item,
                'total' => $this->totinit
            ];
        }
        return $items;
    }

    private function getCodesByDescription($search, BudgetYear $year = null): array
    {
        $year = (null!=$year?$year:$this->year);
        $codes = [];
        //dump($search);
        $items = $this->MCR->findByYear($year, ['code' => 'ASC']);
        foreach ($items as $item) {
            if (array_search($item->getDescription(), $search)!==false) {
                $codes[] = $item->getCode();
            }
        }

        return $codes;
    }

    public function getHospitals(): array
    {
        return $this->hospitals;
    }

    private function getItems($search, $repo): array
    {
        $year = $this->year;
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


    private function getProgs($search, BudgetYear $year = null): array
    {
        $year = (null!=$year?$year:$this->year);
        $items = $this->getItems($search, $this->PR, $year);
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

    public function getSearch(): array
    {
        return $this->search;
    }

    public function getSubconcepts($search, BudgetYear $year = null): array
    {
        $year = (null!=$year?$year:$this->year);
        $items = $this->getItems($search, $this->SCR, $year);
        $this->captiontotal =
        [
            'caption' => [],
            'total' => $this->totinit
        ];
        foreach ($items as $item) {
            $this->captiontotal['caption'][$item->getCode()] = [
                'item' => $item,
                'total' => $this->totinit
            ];
        }
        return $items;
    }

    public function getTotalsFromSub(BudgetYear $year = null): array
    {
        $year = (null!=$year?$year:$this->year);
        $totals = $this->totinit;
        $totalInit = $totalCurrent = $devPos = $devNeg = 0;
        foreach ($this->BIR->findBy(
            [
                'year' => $year,
                'programm' => $this->getProgs($this->search['progs']),
                'subconcept' => $this->getSubconcepts($this->search['subconcepts'])
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
        $totals['caption'] = $this->captiontotal;
        $totals['prog'] = $this->progstotal;
        //dump($totals);

        return $totals;
    }

    public function getTotalsFromCenter(BudgetYear $year = null): array
    {
        $year = (null!=$year?$year:$this->year);
        $totals = $this->totinit;
        $totalInit = $totalCurrent = $devPos = $devNeg = 0;
        foreach ($this->BIR->findBy(
            [
                'year' => $year,
                'programm' => $this->getProgs($this->search['progs']),
                'subconcept' => $this->getSubconcepts($this->search['subconcepts']),
                'center' => $this->getCenters($this->search['centers'])
            ]
        ) as $budget) {
            $caption = $budget->getCenter();
            $code = $caption->getCode();
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
        $totals['caption'] = $this->captiontotal;
        $totals['prog'] = $this->progstotal;
        //dump($totals);

        return $totals;
    }

    public function getTotalsFromItems($items): array
    {
        $this->setSubconcepts($items['subconcepts']['codes']);
        if (!empty($items['progs']['codes'])) {
            $this->setProgrammes($items['progs']['codes'], $items['progs']['exclude']);
        }
        if (!empty($items['subconcepts']['codes'])) {
            $this->setSubconcepts($items['subconcepts']['codes'], $items['subconcepts']['exclude']);
            $t = $this->getTotalsFromSub();
        }
        if (!empty($items['centers'])) {
            if (!empty($items['centers']['codes'])) {
                $this->setCenters($items['centers']['codes'], $items['centers']['exclude']);
                $t = $this->getTotalsFromCenter();
            }
            if (!empty($items['centers']['type'])) {
                $this->setHospitals($items['centers']['type'], $items['centers']['exclude']);
                $t = $this->getTotalsFromCenter();
            }
        }

        return $t;
    }

    public function setCenters($codes, $exclude = false, BudgetYear $year = null): self
    {
        $year = (null!=$year?$year:$this->year);
        if (!is_array($codes)) {
            $codes = [$codes];
        }
        $this->search['centers'] = [
            'codes' => $codes,
            'exclude' => $exclude
        ];

        return $this;
    }

    public function setHospitals($type): self
    {
        if (!empty(self::HOSPITALS[$type])) {
            $this->hospitals = self::HOSPITALS[$type];
            //dump("Poniendo hospitales");
            $this->setCenters($this->getCodesByDescription($this->hospitals));
            //dump($this->hospitals);
        }

        return $this;
    }

    public function setProgrammes($codes, $exclude = false): self
    {
        if (!is_array($codes)) {
            $codes = [$codes];
        }
        $this->search['progs'] = [
            'codes' => $codes,
            'exclude' => $exclude
        ];

        return $this;
    }

    public function setSubconcepts($codes, $exclude = false): self
    {
        if (!is_array($codes)) {
            $codes = [$codes];
        }
        $this->search['subconcepts'] = [
            'codes' => $codes,
            'exclude' => $exclude
        ];

        return $this;
    }

    public function setYear(BudgetYear $year): self
    {
        $this->year = $year;

        return $this;
    }
}
