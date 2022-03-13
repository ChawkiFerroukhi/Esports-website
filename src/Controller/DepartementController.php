<?php

namespace App\Controller;

use App\Entity\Departement;
use App\Form\DepartementType;
use App\Repository\DepartementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dashboard")
 */
class DepartementController extends AbstractController
{
    /**
     * @Route("/departments", name="departement_index", methods={"GET"})
     */
    public function index(DepartementRepository $departementRepository): Response
    {
        $departement = new Departement();
        $form = $this->createForm(DepartementType::class, $departement);
        return $this->render('dashboard/department.html.twig', [
            'departements' => $departementRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/adddepartment", name="departement_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $departement = new Departement();
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($departement);
            $entityManager->flush();

            return $this->redirectToRoute('departement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('departement/new.html.twig', [
            'departement' => $departement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/department/{id}", name="departement_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Departement $departement): Response
    {
        return $this->render('departement/show.html.twig', [
            'departement' => $departement,
        ]);
    }

    /**
     * @Route("/department/{id}/edit", name="departement_edit", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function edit(Request $request, Departement $departement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DepartementType::class, $departement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('departement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('departement/edit.html.twig', [
            'departement' => $departement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/department/delete/{id}", name="departement_delete", methods={"POST"}, requirements={"id":"\d+"})
     */
    public function delete(Request $request, Departement $departement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$departement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($departement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('departement_index', [], Response::HTTP_SEE_OTHER);
    }
}
