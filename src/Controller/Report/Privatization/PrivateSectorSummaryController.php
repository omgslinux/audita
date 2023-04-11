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

#[Route('/report/privatization/privatesectorsummary', name: 'app_report_privatization_private_sector_summary_')]
class PrivateSectorSummaryController extends AbstractController
{
    private $report;

    public function __construct(BudgetReport $report)
    {
        //self::$report = $report;
        $this->report = $report;
    }

    #[Route('/', name: 'index', methods: ['POST'])]
    public function postIndex(Request $request): Response
    {
        $budgets = $bRepo->findByYear($budgetYear);
        return $this->redirectToRoute(self::PREFIX . "show", ['id' => $budgetYear->getId()]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function show(BudgetYear $budgetYear): Response
    {
        $year = $budgetYear->getYear()->format('Y');
        $h1 = "Presupuestos $year: Grupos creados en el informe sobre el papel del sector privado";
        $title = "Comparación presupuesto inicial y liquidado $year";
        $caption = 'Concepto';
        //self::$report->setYear($budgetYear);
        $this->report->setYear($budgetYear);
        /*$report->setSubconcepts(
            [
                25200,
                25206,
                25208,
                25210,
            ],
        );
        $totals = $report->getTotalsFromSub();*/
        $totals = $this->getTotals();
        dump($totals);
        //$report->setHospitals('PPP');
        //$report->setCenters($report->getCodesByDescription($report->getHospitals()));
        //$totals = $report->getTotalsFromCenter($budgetYear);

        return $this->render('report/summary/private_summary.html.twig', [
            'title' => $title,
            'h1' => $h1,
            'totals' => $totals,
            'caption' => $caption,
        ]);
    }

    public function getTotals(): array
    {
        $sections = [
            [
                'title' => 'TRABAJOS REALIZADOS POR OTRAS EMPRESAS',
                'controller' => 'OtherCompanies',
            ],
            [
                'title' => 'ARRENDAMIENTOS',
                'controller' => 'Rentings',
            ],
            [
                'title' => 'CENTRALIZACIÓN DE SERVICIOS: LIMPIEZA, SEGURIDAD, LAVANDERÍA',
                'controller' => 'ServiceCentralization',
            ],
            [
                'title' => 'CONVENIOS LISTA DE ESPERA INTEGRAL',
                'controller' => 'ConventionsIntegralWait',
            ],
            [
                'title' => 'CONVENIOS SUSCRITOS CON INSTITUCIONES SIN FIN DE LUCRO: INSTITUCIONES RELIGIOSAS',
                'controller' => 'ConventionsSFL',
            ],
            [
                'title' => 'CONVENIOS CON ENTIDADES PRIVADAS: LISTAS DE ESPERA, DIÁLISIS Y REHABILITACIÓN',
                'controller' => 'ConventionsDialisisWait',
            ],
            [
                'title' => 'GASTOS FINANCIEROS',
                'controller' => 'FinancialExpenses',
            ],
            [
                'title' => 'INSTRUMENTAL, MATERIAL DE LABORATORIO, QUIRÚRGICO, ASISTENCIAL Y DE CURAS
',
                'controller' => 'SurgeonMaterial',
            ],
            [
                'title' => 'INVERSIONES EXTRA',
                'controller' => 'ExtraInvestments',
            ],
            [
                'title' => 'TRASLADO DE ENFERMOS: SERVICIO DE AMBULANCIAS Y OTROS MEDIOS DE TRANSPORTE',
                'controller' => 'AmbulanceTransport',
            ],
            [
                'title' => 'CONVENIOS CON ENTIDADES PRIVADAS SIN ÁNIMO DE LUCRO: AECC, CRUZ ROJA',
                'controller' => 'ConventionsPrivateSFL',
            ],
            [
                'title' => 'HOSPITALES MODELO PPP',
                'controller' => 'HospitalsPPP',
            ],
            [
                'title' => 'CONCIERTOS CON INSTITUCIONES SANITARIAS: '.
                'FUNDACIÓN JIMÉNEZ DÍAZ, LABORATORIO CLÍNICO CENTRAL',
                'controller' => 'ConventionsFJDLab',
            ],
            [
                'title' => 'HOSPITALES MODELO PFI',
                'controller' => 'HospitalsPFICanon',
            ],
            [
                'title' => 'PRODUCTOS FARMACÉUTICOS Y RECETAS',
                'controller' => 'PharmaProducts',
            ],
            [
                'title' => 'TRABAJOS REALIZADOS POR EMPRESAS DE PROCESO DE DATOS',
                'controller' => 'DataProcessing',
            ],
        ];
        $totals = [];
        foreach ($sections as $section) {
            $this->report->initSearch();
            $name = $section['controller'];
            $subtotal = [];
            //$t = $this->forward($name . 'Controller::getItems');
            //call_user_func($name . 'Controller::getItems', $this->processItems);
            eval('$items = ' . __NAMESPACE__ .'\\' . $name . 'Controller::getItems();');
            dump($items);
            if (!empty($items['progs']['codes'])) {
                $this->report->setProgrammes($items['progs']['codes'], $items['progs']['exclude']);
            }
            if (!empty($items['subconcepts']['codes'])) {
                $this->report->setSubconcepts($items['subconcepts']['codes'], $items['subconcepts']['exclude']);
                $t = $this->report->getTotalsFromSub();
            }
            if (!empty($items['centers']['codes'])) {
                $this->report->setCenters($items['centers']['codes'], $items['centers']['exclude']);
                $t = $this->report->getTotalsFromCenter();
            }
            if (!empty($items['centers']['type'])) {
                $this->report->setHospitals($items['centers']['type'], $items['centers']['exclude']);
                $t = $this->report->getTotalsFromCenter();
            }
            $subtotal = [
                'title' => $section['title'],
                'totals' => $t
            ];

            $totals[$name] = $subtotal;
        }
        return $totals;
    }
}
