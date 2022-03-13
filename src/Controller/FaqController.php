<?php

namespace App\Controller;

use App\Entity\Faq;
use App\Repository\FaqRepository;
use App\Repository\CategoriesRepository;

use App\Entity\Reclamation;
use App\Repository\ReclamationRepository;
use App\Form\ReclamationType;
use App\Form\FaqType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class FaqController extends AbstractController
{

    /**
     * @Route("/faq", name="faq_index")
     */
    public function show(FaqRepository $faqRepository, CategoriesRepository $categoryRepository): Response
    {
        return $this->render('faq/index2.html.twig', [
            'faqs' => $faqRepository->orderByCategory(),
            'categ' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/dashboard/faq", name="faq_dashboard")
     */
    public function dashboardfaq(FaqRepository $faqRepository, CategoriesRepository $categoryRepository): Response
    {
        return $this->render('faq/index3.html.twig', [
            'faqs' => $faqRepository->orderByCategory(),
            'categ' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/faq/{id}", name="app_faq_index", methods={"GET", "POST"})
     */
    public function index(Int $id, ReclamationRepository $reclamationRepository, FaqRepository $faqRepository,  EntityManagerInterface $entityManager , Request $request): Response
    {
       
        $reclamation = new Reclamation();
        $session = $request->getSession();
        $reclamation->setUser($this->getUser());
        $reclamation->setSujet($session->get('rec_sujet'));
        $reclamation->setDescription($session->get('rec_description'));
        $reclamation->setEmail($session->get('rec_email'));
        $reclamation->setDate($session->get('rec_date'));
        $reclamation->setStatus($session->get('rec_status'));
        $reclamation->setCategory($session->get('rec_category'));
        $entityManager->persist($reclamation);
                
       
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationRepository->add($reclamation);
            $entityManager->flush();   
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('faq/index.html.twig', [
            'faqs' => $faqRepository->findByID($id),
            'reclamation'=> $reclamation,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/faq/new", name="app_faq_new", methods={"GET", "POST"})
     */
    public function new(Request $request, FaqRepository $faqRepository): Response
    {
        $faq = new Faq();
        $form = $this->createForm(FaqType::class, $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $faqRepository->add($faq);
            return $this->redirectToRoute('app_faq_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('faq/new.html.twig', [
            'faq' => $faq,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/faq/{id}/edit", name="app_faq_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Faq $faq, FaqRepository $faqRepository): Response
    {
        $form = $this->createForm(FaqType::class, $faq);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $faqRepository->add($faq);
            return $this->redirectToRoute('app_faq_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('faq/edit.html.twig', [
            'faq' => $faq,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/faq/{id}", name="app_faq_delete", methods={"POST"})
     */
    public function delete(Request $request, Faq $faq, FaqRepository $faqRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$faq->getId(), $request->request->get('_token'))) {
            $faqRepository->remove($faq);
        }

        return $this->redirectToRoute('app_faq_index', [], Response::HTTP_SEE_OTHER);
    }
}
