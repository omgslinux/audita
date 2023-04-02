<?php

namespace App\Controller\Report;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReportIndexController extends AbstractController
{
    const REPORTS = [
        'blue' => [
            [
                'title' => 'Resumen anual',
                'path' => 'app_report_summary_',
            ],
            [
                'title' => 'Por capitulos',
                'path' => 'app_report_chapters_',
            ],
            [
                'title' => 'Inversiones',
                'path' => 'app_report_investings_',
            ],
            [
                'title' => 'Gastos de personal',
                'path' => 'app_report_chapter1_',
            ],
            [
                'title' => 'Hospitales públicos nivel 3',
                'path' => 'app_report_public_hospitalsL3_',
            ],
            [
                'title' => 'Hospitales modelo PFI',
                'path' => 'app_report_PFIHospitals_',
            ],
            [
                'title' => 'Programas',
                'path' => 'app_report_programmes_',
            ],
            [
                'title' => 'Clas. Económica - Detalle',
                'path' => 'app_report_budget_detail_',
            ],
            [
                'title' => 'Centros gestores',
                'path' => 'app_report_centers_',
            ],
        ],
        'green' => [
            [
                'title' => 'Informe papel sector privado',
                'path' => 'app_report_private_role_'
            ],
            [
                'title' => 'Trabajos otras empresas',
                'path' => 'app_report_other_companies_'
            ],
            [
                'title' => 'Centralización de servicios',
                'path' => 'app_report_services_central_'
            ],
            [
                'title' => 'Convenios instituciones SFL',
                'path' => 'app_report_conventions_SFL_'
            ],
            [
                'title' => 'Convenios listas de espera integral',
                'path' => 'app_report_integral_await_'
            ],
            [
                'title' => 'Arrendamientos',
                'path' => 'app_report_renting_'
            ],
            [
                'title' => 'Gasto financiero',
                'path' => 'app_report_financial_expenses_'
            ],
            [
                'title' => 'Instrumental material quirúrgico',
                'path' => 'app_report_surgeon_tools_'
            ],
            [
                'title' => 'Inversiones extra',
                'path' => 'app_report_extra_investments_'
            ],
            [
                'title' => 'Traslado ambulancias',
                'path' => 'app_report_ambulance_transport_'
            ],
            [
                'title' => 'Convenios entidades privadas SFL AECC, Cruz Roja',
                'path' => 'app_report_private_SFL_organizations_'
            ],
            [
                'title' => 'Hospitales model PPP',
                'path' => 'app_report_PPP_model_hospitals_'
            ],
            [
                'title' => 'Gasto hospitales modelo PFI',
                'path' => 'app_report_PFI_model_expenses_'
            ],
            [
                'title' => 'Canon hospitales modelo PFI',
                'path' => 'app_report_PFI_model_canon_'
            ],
            [
                'title' => 'Productos farmacéuticos',
                'path' => 'app_report_pharma_products_'
            ],
            [
                'title' => 'Proceso de datos',
                'path' => 'app_report_data_processing_'
            ],
            [
                'title' => 'Conciertos con instituciones FJD y laboratorio',
                'path' => 'app_report_FJD_laboratory_'
            ],
        ]
    ];

    #[Route('/report/index', name: 'app_report_index')]
    public function index(): Response
    {
        $title = "Indice de informes";
        $h1 = $title;
        return $this->render('report/index.html.twig', [
            'title' => $title,
            'h1' => $h1,
            'REPORTS' => self::REPORTS,
        ]);
    }
}