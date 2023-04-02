<?php

namespace App\Controller\Admin;

use App\Entity\BudgetItem;
use App\Entity\BudgetYear;
use App\Form\BudgetDataType;
use App\Form\BudgetYearType;
use App\Repository\BudgetItemRepository;
use App\Repository\BudgetYearRepository;
use App\Repository\ManagementCenterRepository;
use App\Repository\ProgrammRepository;
use App\Repository\SubconceptRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/budget/year', name: 'admin_budget_year')]
class BudgetYearController extends AbstractController
{
    const PREFIX = 'admin_budget_year';
    private $repo;

    public function __construct(BudgetYearRepository $budgetYearRepository)
    {
        $this->repo = $budgetYearRepository;
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('budget_year/index.html.twig', [
            'budget_years' => $this->repo->findAll(),
            'PREFIX' => self::PREFIX,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, BudgetYearRepository $budgetYearRepository): Response
    {
        $budgetYear = new BudgetYear();
        $form = $this->createForm(BudgetYearType::class, $budgetYear);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budgetYearRepository->save($budgetYear, true);

            return $this->redirectToRoute(self::PREFIX . 'index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('budget_year/new.html.twig', [
            'budget_year' => $budgetYear,
            'form' => $form,
            'PREFIX' => self::PREFIX,
        ]);
    }

    #[Route('/{id}/show', name: 'show', methods: ['GET'])]
    public function show(BudgetYear $budgetYear): Response
    {

        $form = $this->createForm(BudgetDataType::class, null, [
            'action' => $this->generateUrl(self::PREFIX . 'addBudgetItems', ['id' => $budgetYear->getId()]),
        ]);

        return $this->render('budget_year/show.html.twig', [
            'budget_year' => $budgetYear,
            'PREFIX' => self::PREFIX,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BudgetYear $budgetYear): Response
    {
        $form = $this->createForm(BudgetYearType::class, $budgetYear);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->save($budgetYear, true);

            return $this->redirectToRoute(self::PREFIX . 'index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('budget_year/edit.html.twig', [
            'budget_year' => $budgetYear,
            'form' => $form,
            'PREFIX' => self::PREFIX,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, BudgetYear $budgetYear): Response
    {
        if ($this->isCsrfTokenValid('delete'.$budgetYear->getId(), $request->request->get('_token'))) {
            $this->repo->remove($budgetYear, true);
        }

        return $this->redirectToRoute(self::PREFIX . 'index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/addBudgetItems', name: 'addBudgetItems', methods: ['GET', 'POST'])]
    public function addBudgetItems(
        Request $request,
        BudgetYear $entity,
        ManagementCenterRepository $MCR,
        ProgrammRepository $PGR,
        SubconceptRepository $SCR,
        BudgetItemRepository $BIR
    ): Response {
        $form = $this->createForm(BudgetDataType::class, null, [
            //'action' => $this->generateUrl(self::PREFIX . 'addBudgetItems'),
            //'BudgetYear' => $entity
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            //dump($data);
            //die();
            /*
                Código organo{tab}Organo{tab}Cprograma{tab}Programa{tab}Csub{tab}Subconcepto{tab}CreditoInicial[{tab}Obligaciones]
                170010000{tab}S.G.T. DE SANIDAD{tab}311M{tab}DIRECCIÓN Y GESTIÓN ADMINISTRATIVA DE SANIDAD{tab}10000{tab} RETRIBUCIONES BÁSICAS ALTOS CARGOS	339110.00
                170010000	S.G.T. DE SANIDAD	311M	DIRECCIÓN Y GESTIÓN ADMINISTRATIVA DE SANIDAD	11000	 RETRIBUCIONES BÁSICAS PERSONAL EVENTUAL DE GABINETES	80041.00
                170010000	S.G.T. DE SANIDAD	311M	DIRECCIÓN Y GESTIÓN ADMINISTRATIVA DE SANIDAD	11001	 OTRAS REMUNERACIONES PERSONAL EVENTUAL DE GABINETES	206315.00
            */
            $_center = $_programm = "";
            $params = $request->request->all();
            foreach ($params as $param) {
                if (!empty($param['data'])) {
                    foreach (preg_split("/((\r?\n)|(\r\n?))/", $param['data']) as $line) {
                    //while (($line = fgetcsv($data, 1000, "\t")) !== false) {
                        dump($line);
                        if (!empty($line)) {
                            $items = explode("\t", $line);
                            dump($items);
                            list($centerCode, $centerDescription, $programmCode, $programmDescription) = $items;
                            //$programmDescription, $subCode, $subDescription, $_credits) = explode("\t", $line);
                            if ($_center != $centerCode) {
                                dump($centerCode, $centerDescription);
                                $_center = trim($centerCode);
                                $center = $MCR->findOneByCode($_center);
                                if (null==$center) {
                                    $center = new \App\Entity\ManagementCenter();
                                    $center->setCode((string)$_center)
                                    ->setDescription(trim($centerDescription))
                                    ->setYear($entity);
                                    $MCR->save($center, true);
                                }
                            }
                            if ($_programm != $programmCode) {
                                dump($programmCode, $programmDescription);
                                $_programm = trim($programmCode);
                                $programm = $PGR->findOneByCode($_programm);
                                if (null==$programm) {
                                    $programm = new \App\Entity\Programm();
                                    $programm->setCode((string)$_programm)
                                    ->setDescription(trim($programmDescription))
                                    ->setYear($entity);
                                    $PGR->save($programm, true);
                                }
                            }
                            if (!empty($items[4]) && strlen(trim($items[4]))) {
                                $subCode = trim($items[4]);
                                $subDescription = trim($items[5]);
                                $subConcept = $SCR->findOneByCode($subCode);
                                dump($subCode, $subConcept);
                                if (null==$subConcept) {
                                    $subConcept = new \App\Entity\Subconcept();
                                    $subConcept->setCode((string) $subCode)
                                    ->setDescription($subDescription)
                                    ->setYear($entity);
                                    $SCR->save($subConcept, true);
                                }

                                $budgetItem = $BIR->findOneBy(
                                    [
                                        'year' => $entity,
                                        'center' => $center,
                                        'programm' => $programm,
                                        'subconcept' => $subConcept
                                    ]
                                );
                                if (null==$budgetItem) {
                                    $budgetItem = new BudgetItem();
                                    $budgetItem
                                    ->setYear($entity)
                                    ->setCenter($center)
                                    ->setProgramm($programm)
                                    ->setSubconcept($subConcept);
                                }
                                $iniCredit = trim($items[6]);
                                $currentCredit = (!empty($items[7])?(float)trim($credits[7]):0);
                                $budgetItem->setInitialCredit((float)$iniCredit)
                                ->setCurrentCredit(!empty($currentCredit)?$currentCredit:0);
                                //$entity->addBudgetItem($budgetItem);
                                //$this->repo->save($entity, true);
                                $BIR->save($budgetItem, true);
                            }
                        }
                    }
                }
            }

            return $this->redirectToRoute(self::PREFIX . 'index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('budget_year/new.html.twig', [
            'form' => $form,
            'PREFIX' => self::PREFIX,
        ]);
    }


}
