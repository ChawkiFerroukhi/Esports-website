<?php

namespace App\Controller;

use App\Entity\Jeux;
use App\Entity\Review;
use App\Form\JeuxType;
use App\Form\ReviewType;
use App\Repository\EquipeRepository;
use App\Repository\JeuxRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class JeuxController extends AbstractController
{
    /**
     * @Route("/dashboard/games", name="jeux_index", methods={"GET", "POST"})
     */
    public function index(JeuxRepository $jeuxRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $jeux = new Jeux();
        $form = $this->createForm(JeuxType::class, $jeux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jeux);
            $entityManager->flush();

            return $this->redirectToRoute('jeux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('jeux/index.html.twig', [
            'jeuxes' => $jeuxRepository->couuunt(),
            'form' => $form->createView(),

        ]);
    }
    /**
     * @Route("/dashboardindexee", name="jeux_indexee", methods={"GET"})
     */
    public function indexeee(JeuxRepository $jeuxRepository): Response
    {

        $jeux = new Jeux();
        $form = $this->createForm(JeuxType::class, $jeux);
        return $this->render('jeux/index.html.twig', [
            'jeuxes' => $jeuxRepository->couuuntee(),
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/games", name="jeux_frontindex", methods={"GET"})
     */
    public function indexfront(JeuxRepository $jeuxRepository): Response
    {
        return $this->render('jeux/afficherj.html.twig', [
            'jeuxes' => $jeuxRepository->findAll(),
        ]);
    }
    /**
     * @param JeuxRepository $repository
     * @Route ("/games/{id}", name="affichfrontdetail")
     */
    public function Affichefrontdetail(JeuxRepository   $repository,$id, Request $requeste, ReviewRepository $rep)
    {
        $jeu=$repository->find($id);
        $review=new Review();
$review->setJeuxId($jeu);

        $form=$this->createForm(ReviewType::class,$review);
        $form->handleRequest($requeste);

        if($form->isSubmitted() && $form->isValid())
        {

            $em=$this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();
            return $this->redirectToRoute('jeux_frontindex');


        }


        return $this->render('jeux/detail.html.twig',[
            'jeu'=>$jeu,
            'f'=>$form->createView()
        ]);






    }

    /**
     * @Route("/game/new", name="jeux_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jeux = new Jeux();
        $form = $this->createForm(JeuxType::class, $jeux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jeux);
            $entityManager->flush();

            return $this->redirectToRoute('jeux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('jeux/new.html.twig', [
            'jeux' => $jeux,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/game/{id}", name="jeux_show", methods={"GET"})
     */
    public function show(Jeux $jeux): Response
    {
        return $this->render('jeux/show.html.twig', [
            'jeux' => $jeux,
        ]);
    }

    /**
     * @Route("/game/{id}/edit", name="jeux_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Jeux $jeux, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JeuxType::class, $jeux);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('jeux_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('jeux/edit.html.twig', [
            'jeux' => $jeux,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/game/{id}", name="jeux_delete", methods={"POST"})
     */
    public function delete(Request $request, Jeux $jeux, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jeux->getId(), $request->request->get('_token'))) {
            $entityManager->remove($jeux);
            $entityManager->flush();
        }

        return $this->redirectToRoute('jeux_index', [], Response::HTTP_SEE_OTHER);
    }
}