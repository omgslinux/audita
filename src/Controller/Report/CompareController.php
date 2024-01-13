<?php

namespace App\Controller\Report;

use App\Entity\BudgetYear;
use App\Repository\BudgetChapterRepository as BCRepo;
use App\Repository\BudgetItemRepository as BIRepo;
use App\Repository\ManagementCenterRepository as MCR;
use App\Repository\ProgrammRepository as PR;
use App\Repository\SubconceptRepository as SR;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/compare', name: 'app_report_compare_')]
class CompareController extends AbstractController
{
    #[Route('/center/{code}/show', name: 'center', methods: ['GET'])]
    public function center(BIRepo $bRepo, $code): Response
    {
        //$year = $budgetYear->getYear()->format('Y');
        $h1 = "Comparativa interanual centro $code";
        $title = "Comparación presupuesto inicial y liquidado";
        //$caption = 'Descripción';
        $totals = [
            'caption',
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        $years=[];
        foreach ($bRepo->findByCenterCode($code) as $budget) {
            //dump($budget, $budget->getYear()->getYear()->format('Y'));
            $year = $budget->getYear()->getYear()->format('Y');
            if (empty($years[$year])) {
                $years[$year] = [
                    'item' => $budget,
                    'caption' => $budget->getCenter(),
                    'totalInit' => 0,
                    'totalCurrent' => 0,
                    'devPos' => 0,
                    'devNeg' => 0
                ];
            }
            $yearTotals = $years[$year];
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $yearTotals['totalInit'] += $init;
            $yearTotals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
              $yearTotals['devPos'] += $deviation;
            } else {
              $yearTotals['devNeg'] += $deviation;
            }
            $years[$year] = $yearTotals;
        }

        return $this->renderCommon($title, $h1, $years, $code);
    }

    #[Route('/center/{code}/chapter/{chapter}/show', name: 'center_chapter', methods: ['GET'])]
    public function centerAndChapter(BIRepo $bRepo, $code, $chapter): Response
    {
        //$year = $budgetYear->getYear()->format('Y');
        $h1 = "Comparativa interanual centro $code y capítulo $chapter";
        $title = "Comparación presupuesto inicial y liquidado";
        //$caption = 'Descripción';
        $totals = [
            'caption',
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        $years=[];
        foreach ($bRepo->findByCenterAndChapterCode($code, $chapter) as $budget) {
            //dump($budget, $budget->getYear()->getYear()->format('Y'));
            $year = $budget->getYear()->getYear()->format('Y');
            if (empty($years[$year])) {
                $years[$year] = [
                    'item' => $budget,
                    'caption' => $budget->getCenter(),
                    'totalInit' => 0,
                    'totalCurrent' => 0,
                    'devPos' => 0,
                    'devNeg' => 0
                ];
            }
            $yearTotals = $years[$year];
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $yearTotals['totalInit'] += $init;
            $yearTotals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
              $yearTotals['devPos'] += $deviation;
            } else {
              $yearTotals['devNeg'] += $deviation;
            }
            $years[$year] = $yearTotals;
        }

        return $this->renderCommon($title, $h1, $years, $code);
    }

    #[Route('/center/{code}/programm/{pcode}/show', name: 'center_programm', methods: ['GET'])]
    public function centerAndProgramm(BIRepo $bRepo, $code, $pcode): Response
    {
        //$year = $budgetYear->getYear()->format('Y');
        $h1 = "Comparativa interanual centro $code y programa $pcode";
        $title = "Comparación presupuesto inicial y liquidado";
        //$caption = 'Descripción';
        $totals = [
            'caption',
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        $years=[];
        foreach ($bRepo->findByCenterAndProgrammCode($code, $pcode) as $budget) {
            //dump($budget, $budget->getYear()->getYear()->format('Y'));
            $year = $budget->getYear()->getYear()->format('Y');
            if (empty($years[$year])) {
                $years[$year] = [
                    'item' => $budget,
                    'caption' => $budget->getCenter(),
                    'totalInit' => 0,
                    'totalCurrent' => 0,
                    'devPos' => 0,
                    'devNeg' => 0
                ];
            }
            $yearTotals = $years[$year];
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $yearTotals['totalInit'] += $init;
            $yearTotals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
              $yearTotals['devPos'] += $deviation;
            } else {
              $yearTotals['devNeg'] += $deviation;
            }
            $years[$year] = $yearTotals;
        }

        return $this->renderCommon($title, $h1, $years, $code);
    }

    #[Route('/center/{code}/subconcept/{scode}/show', name: 'center_subconcept', methods: ['GET'])]
    public function centerAndSubconcept(BIRepo $bRepo, $code, $scode): Response
    {
        //$year = $budgetYear->getYear()->format('Y');
        $h1 = "Comparativa interanual centro $code y subconcepto $scode";
        $title = "Comparación presupuesto inicial y liquidado";
        //$caption = 'Descripción';
        $totals = [
            'caption',
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        $years=[];
        foreach ($bRepo->findByCenterAndSubconceptCode($code, $scode) as $budget) {
            //dump($budget, $budget->getYear()->getYear()->format('Y'));
            $year = $budget->getYear()->getYear()->format('Y');
            if (empty($years[$year])) {
                $years[$year] = [
                    'item' => $budget,
                    'caption' => $budget->getCenter(),
                    'totalInit' => 0,
                    'totalCurrent' => 0,
                    'devPos' => 0,
                    'devNeg' => 0
                ];
            }
            $yearTotals = $years[$year];
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $yearTotals['totalInit'] += $init;
            $yearTotals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
              $yearTotals['devPos'] += $deviation;
            } else {
              $yearTotals['devNeg'] += $deviation;
            }
            $years[$year] = $yearTotals;
        }

        return $this->renderCommon($title, $h1, $years, $code);
    }

    #[Route('/chapter/{code}/show', name: 'chapter', methods: ['GET'])]
    public function chapter(BIRepo $bRepo, BCRepo $bcRepo, $code): Response
    {
        //$year = $budgetYear->getYear()->format('Y');
        $h1 = "Comparativa interanual capítulo $code";
        $title = "Comparación presupuesto inicial y liquidado";
        //$caption = 'Descripción';
        $totals = [
            'caption',
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        $years=[];
        $description = $bcRepo->findByChapterCode($code)->getDescription();
        foreach ($bRepo->findByChapterCode($code) as $budget) {
            //dump($budget, $budget->getYear()->getYear()->format('Y'));
            $year = $budget->getYear()->getYear()->format('Y');
            if (empty($years[$year])) {
                $years[$year] = [
                    'item' => $budget,
                    'caption' => $bcRepo->findByChapterCode($code),
                    'totalInit' => 0,
                    'totalCurrent' => 0,
                    'devPos' => 0,
                    'devNeg' => 0
                ];
            }
            $yearTotals = $years[$year];
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $yearTotals['totalInit'] += $init;
            $yearTotals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
              $yearTotals['devPos'] += $deviation;
            } else {
              $yearTotals['devNeg'] += $deviation;
            }
            $years[$year] = $yearTotals;
        }

        return $this->renderCommon($title, $h1, $years, $code);
    }


    #[Route('/program/{code}/show', name: 'programm', methods: ['GET'])]
    public function program(BIRepo $bRepo, $code): Response
    {
        //$year = $budgetYear->getYear()->format('Y');
        $h1 = "Comparativa interanual programa $code";
        $title = "Comparación presupuesto inicial y liquidado";
        //$caption = 'Descripción';
        $totals = [
            'caption',
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        $years=[];
        foreach ($bRepo->findByProgramCode($code) as $budget) {
            //dump($budget, $budget->getYear()->getYear()->format('Y'));
            $year = $budget->getYear()->getYear()->format('Y');
            if (empty($years[$year])) {
                $years[$year] = [
                    'item' => $budget,
                    'caption' => $budget->getProgramm(),
                    'totalInit' => 0,
                    'totalCurrent' => 0,
                    'devPos' => 0,
                    'devNeg' => 0
                ];
            }
            $yearTotals = $years[$year];
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $yearTotals['totalInit'] += $init;
            $yearTotals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
              $yearTotals['devPos'] += $deviation;
            } else {
              $yearTotals['devNeg'] += $deviation;
            }
            $years[$year] = $yearTotals;
        }

        return $this->renderCommon($title, $h1, $years, $code);
    }

    #[Route('/program/{code}/chapter/{chapter}/show', name: 'programm_chapter', methods: ['GET'])]
    public function programAndChapter(BIRepo $bRepo, $code, $chapter): Response
    {
        //$year = $budgetYear->getYear()->format('Y');
        $h1 = "Comparativa interanual programa $code y capítulo $chapter";
        $title = "Comparación presupuesto inicial y liquidado";
        //$caption = 'Descripción';
        $totals = [
            'caption',
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        $years=[];
        foreach ($bRepo->findByProgramAndChapterCode($code, $chapter) as $budget) {
            //dump($budget, $budget->getYear()->getYear()->format('Y'));
            $year = $budget->getYear()->getYear()->format('Y');
            if (empty($years[$year])) {
                $years[$year] = [
                    'item' => $budget,
                    'caption' => $budget->getProgramm(),
                    'totalInit' => 0,
                    'totalCurrent' => 0,
                    'devPos' => 0,
                    'devNeg' => 0
                ];
            }
            $yearTotals = $years[$year];
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $yearTotals['totalInit'] += $init;
            $yearTotals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
              $yearTotals['devPos'] += $deviation;
            } else {
              $yearTotals['devNeg'] += $deviation;
            }
            $years[$year] = $yearTotals;
        }

        return $this->renderCommon($title, $h1, $years, $code);
    }

    #[Route('/program/{pcode}/subconcept/{scode}/show', name: 'programm_subconcept', methods: ['GET'])]
    public function programAndSubconceptCode(BIRepo $bRepo, $pcode, $scode): Response
    {
        //$year = $budgetYear->getYear()->format('Y');
        $h1 = "Comparativa interanual programa $pcode y subconcepto $scode";
        $title = "Comparación presupuesto inicial y liquidado";
        //$caption = 'Descripción';
        $totals = [
            'caption',
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        $years=[];
        foreach ($bRepo->findByProgramAndSubconceptCode($pcode, $scode) as $budget) {
            //dump($budget, $budget->getYear()->getYear()->format('Y'));
            $year = $budget->getYear()->getYear()->format('Y');
            if (empty($years[$year])) {
                $years[$year] = [
                    'item' => $budget,
                    'caption' => $budget->getSubconcept(),
                    'totalInit' => 0,
                    'totalCurrent' => 0,
                    'devPos' => 0,
                    'devNeg' => 0
                ];
            }
            $yearTotals = $years[$year];
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $yearTotals['totalInit'] += $init;
            $yearTotals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
              $yearTotals['devPos'] += $deviation;
            } else {
              $yearTotals['devNeg'] += $deviation;
            }
            $years[$year] = $yearTotals;
        }

        return $this->renderCommon($title, $h1, $years, $pcode);
    }


    #[Route('/subconcept/{code}/show', name: 'subconcept', methods: ['GET'])]
    public function subconcept(BIRepo $bRepo, $code): Response
    {
        //$year = $budgetYear->getYear()->format('Y');
        $h1 = "Comparativa interanual subconcepto $code";
        $title = "Comparación presupuesto inicial y liquidado";
        //$caption = 'Descripción';
        $totals = [
            'caption',
            'totalInit' => 0,
            'totalCurrent' => 0,
            'devPos' => 0,
            'devNeg' => 0
        ];
        $years=[];
        foreach ($bRepo->findBySubconceptCode($code) as $budget) {
            //dump($budget, $budget->getYear()->getYear()->format('Y'));
            $year = $budget->getYear()->getYear()->format('Y');
            if (empty($years[$year])) {
                $years[$year] = [
                    'item' => $budget,
                    'caption' => $budget->getSubconcept(),
                    'totalInit' => 0,
                    'totalCurrent' => 0,
                    'devPos' => 0,
                    'devNeg' => 0
                ];
            }
            $yearTotals = $years[$year];
            $init = $budget->getInitialCredit();
            $current = $budget->getCurrentCredit();
            $yearTotals['totalInit'] += $init;
            $yearTotals['totalCurrent'] += $current;
            $deviation = $current - $init;
            if ($deviation>0) {
              $yearTotals['devPos'] += $deviation;
            } else {
              $yearTotals['devNeg'] += $deviation;
            }
            $years[$year] = $yearTotals;
        }

        return $this->renderCommon($title, $h1, $years, $code);
    }

    private function renderCommon($title, $h1, $years, $code)
    {
        sort($years, ksort($years));

        return $this->render('report/summary/compare_summary.html.twig', [
            'title' => $title,
            'h1' => $h1,
            'totals' => $years,
            //'caption' => $caption,
            'code' => $code,
        ]);
    }
}
