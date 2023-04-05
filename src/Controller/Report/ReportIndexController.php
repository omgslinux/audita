<?php

namespace App\Controller\Report;

use App\Entity\BudgetYear;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ReportIndexController extends AbstractController
{
    const REPORTS = [
        'blue' => [
            [
                'title' => 'Resumen anual',
                'path' => 'app_report_summary_byyear_',
            ],
            [
                'title' => 'Por capitulos',
                'path' => 'app_report_summary_bychapter_',
            ],
            [
                'title' => 'Inversiones',
                'path' => 'app_report__summary_investments_',
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
                'path' => 'app_report_summary_by_programm_',
            ],
            [
                'title' => 'Clas. Económica - Detalle',
                'path' => 'app_report_summary_subconcept_detail_',
            ],
            [
                'title' => 'Centros gestores',
                'path' => 'app_report_summary_by_center_',
            ],
        ],
        'green' => [
            [
                'title' => 'Informe papel sector privado',
                'path' => 'app_report_private_role_'
            ],
            [
                'title' => 'Trabajos otras empresas',
                'path' => 'app_report_privatization_other_companies_'
            ],
            [
                'title' => 'Centralización de servicios',
                'path' => 'app_report_privatization_services_central_'
            ],
            [
                'title' => 'Convenios instituciones SFL',
                'path' => 'app_report_privatization_conventions_SFL_'
            ],
            [
                'title' => 'Convenios listas de espera integral',
                'path' => 'app_report_privatization_conventions_integral_wait_'
            ],
            [
                'title' => 'Convenios listas de espera diálisis',
                'path' => 'app_report_privatization_conventions_dialisis_wait_'
            ],
            [
                'title' => 'Arrendamientos',
                'path' => 'app_report_privatization_renting_'
            ],
            [
                'title' => 'Gasto financiero',
                'path' => 'app_report_privatization_financial_expenses_'
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

    #[Route('/report/index', name: 'app_report_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $budgetYear = new BudgetYear();
        $form = $this->createFormBuilder($budgetYear->getYear())
        ->add(
            'year',
            EntityType::class,
            [
                'class' => BudgetYear::class,
                //'data' => $budgetYear,
                'multiple' => false,
                'expanded' => false,
            ]
        )
        ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$budgetYearRepository->save($budgetYear, true);
            $yearId =$request->get('form')['year'];
            $path = $request->get('report')[0];
            return $this->redirectToRoute($path . 'show', ['id' => $yearId ], Response::HTTP_SEE_OTHER);

            //return $this->redirectToRoute(self::PREFIX . 'index', [], Response::HTTP_SEE_OTHER);
        }

        $title = "Indice de informes";
        $h1 = $title;
        return $this->render('report/index.html.twig', [
            'title' => $title,
            'h1' => $h1,
            'form' => $form,
            'REPORTS' => self::REPORTS,
        ]);
    }
}
