<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Form\EquipeType;
use App\Form\ReviewType;
use App\Repository\EquipeRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Review;
use App\Repository\ClassementRepository;
use App\Repository\EvenementRepository;
use App\Repository\JeuxRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @Route("/")
 */
class EquipeController extends AbstractController
{
    /**
     * @Route("/dashboard/teams", name="equipe_index", methods={"GET", "POST"})
     */
    public function index(EquipeRepository $equipeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipe);
            $entityManager->flush();

            return $this->redirectToRoute('equipe_index', [], Response::HTTP_SEE_OTHER);
        }
        /*$allequipes = $this->get('knp_paginator')->paginate(
// Doctrine Query, not results
            $equipe,
            // Define the page parameter
            $request->query->getInt('page', 1),
            2
        );*/



        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipeRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route ("/Imprimer/{id}" ,name="imp")
     */
    public function pdf($id,EquipeRepository  $adresseRepository)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $produit=$adresseRepository->find($id);


        $html = $this->renderView('equipe/imp.html.twig',
            ['equipes' => $produit
            ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);
    }


    /**
     * @Route("/teams", name="equipe_frontindex", methods={"GET"})
     */
    public function indexfront(EquipeRepository  $equipeRepository): Response
    {
        return $this->render('equipe/afficherE.html.twig', [
            'equipes' => $equipeRepository->findAll(),
        ]);
    }
    /**
     * @Route("/new", name="equipe_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipe);
            $entityManager->flush();

            return $this->redirectToRoute('equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/team/{id}", name="equipe_show", methods={"GET"})
     */
    public function show(Equipe $equipe): Response
    {
        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    /**
     * @Route("/team/{id}/edit", name="equipe_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/team/{id}", name="equipe_delete", methods={"POST"})
     */
    public function delete(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('equipe_index', [], Response::HTTP_SEE_OTHER);
    }



}