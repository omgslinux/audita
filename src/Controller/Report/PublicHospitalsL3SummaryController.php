<?php

namespace App\Controller\Report;

use App\Entity\BudgetYear;
use App\Repository\BudgetChapterRepository as BCR;
use App\Repository\BudgetItemRepository;
use App\Repository\ManagementCenterRepository as MCR;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/summary/publicThirdLevel', name: 'app_report_public_hospitalsL3_')]
class PublicHospitalsL3SummaryController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['POST'])]
    public function postIndex(Request $request): Response
    {
        $budgets = $bRepo->findByYear($budgetYear);
        return $this->redirectToRoute(self::PREFIX . "show", ['id' => $budgetYear->getId()]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function index(BudgetItemRepository $bRepo, MCR $MCR, BCR $BCR, BudgetYear $budgetYear): Response
    {
        $year = $budgetYear->getYear()->format('Y');
        $h1 = "Presupuestos $year: Hospitales públicos nivel 3";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $totals = [
            'chapter' =>
            [
            ],
            'totals' =>
            [
                'totalInit' => 0,
                'totalCurrent' => 0,
                'devPos' => 0,
                'devNeg' => 0
            ]
        ];
        $l3Hospitals = [
            'HOSPITAL UNIVERSITARIO LA PAZ',
            'HOSPITAL UNIVERSITARIO 12 DE OCTUBRE',
            'HOSPITAL UNIVERSITARIO RAMÓN Y CAJAL',
            'HOSPITAL CLÍNICO SAN CARLOS',
            'HOSPITAL UNIVERSITARIO DE LA PRINCESA',
            'HOSPITAL GENERAL UNIV. GREGORIO MARAÑÓN',
        ];
        $centers = $MCR->findBy(
            [
                'year' => $budgetYear,
                'description' => $l3Hospitals,
            ]
        );
        // Necesitamos las descripciones de los capítulos
        $chapters = [];
        foreach ($BCR->findAll() as $chapter) {
            $code = $chapter->getCode();
            if ($code < 10) {
                $chapters[$code] = $chapter->getDescription();
            }
        }dump($chapters);

        $budgets = $bRepo->findBy(
            [
                'year' => $budgetYear,
                'center' => $centers
            ]
        );

        $totalInit = $totalCurrent = $devPos = $devNeg = 0;
        foreach ($budgets as $budget) {
            //dump($budget);
            $chaptercode = $budget->getSubconcept()->getChapter();
            if (empty($totals['chapter'][$chaptercode])) {
                $totals['chapter'][$chaptercode] = [
                    'centers' => [],
                    'totals' =>
                    [
                        'totalInit' => 0,
                        'totalCurrent' => 0,
                        'devPos' => 0,
                        'devNeg' => 0
                    ]
                ];
            }
            $center = $budget->getCenter();
            $centercode = $center->getCode();
            if (empty($totals['chapter'][$chaptercode]['centers'][$centercode])) {
                $totals['chapter'][$chaptercode]['centers'][$centercode] = [
                    'center' => $center,
                    'totals' =>
                    [
                        'totalInit' => 0,
                        'totalCurrent' => 0,
                        'devPos' => 0,
                        'devNeg' => 0
                    ]
                ];
            }
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $totals['chapter'][$chaptercode]['totals']['totalInit'] += $init;
            $totals['chapter'][$chaptercode]['centers'][$centercode]['totals']['totalInit'] += $init;
            $totals['totals']['totalInit'] += $init;
            $totals['chapter'][$chaptercode]['totals']['totalCurrent'] += $current;
            $totals['chapter'][$chaptercode]['centers'][$centercode]['totals']['totalCurrent'] += $current;
            $totals['totals']['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
                $totals['chapter'][$chaptercode]['totals']['devPos'] += $deviation;
                $totals['chapter'][$chaptercode]['centers'][$centercode]['totals']['devPos'] += $deviation;
                $totals['totals']['devPos'] += $deviation;
            } else {
                $totals['chapter'][$chaptercode]['totals']['devNeg'] += $deviation;
                $totals['chapter'][$chaptercode]['centers'][$centercode]['totals']['devNeg'] += $deviation;
                $totals['totals']['devNeg'] += $deviation;
            }
        }
        dump($totals);

        return $this->render('report/summary/public_third_level_summary.html.twig', [
            //'budgets' => $budgets,
            'title' => $title,
            'h1' => $h1,
            'chapters' => $chapters,
            'totals' => $totals,
        ]);
    }
}
