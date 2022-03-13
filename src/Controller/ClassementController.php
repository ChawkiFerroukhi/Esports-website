<?php

namespace App\Controller;

use App\Entity\Classement;
use App\Entity\Evenement;
use src\Form\ClassementType;
use src\Form\EditClassementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClassementController extends AbstractController
{

    private $js="";
    /**
     * @Route("/classement", name="app_classement")
     */
    public function index(): Response
    {
        $p = $this->getDoctrine()->getRepository(Classement::class)->findAll();
        $events = $this->getDoctrine()->getRepository(Evenement::class)->findAll();
        return $this->render('classement/classement_front.html.twig',
            array('classement' => $p, "events"=> $events));
    }

    /**
     * @Route("/dashboard/admin_classements", name="show_classement_admin")
     * Method({"GET"})
     */
    public function getEventsAdmin()
    {
        $p = $this->getDoctrine()->getRepository(Classement::class)->findAll();

        return $this->render('classement/afficher_classement_admin.html.twig',
            array('classement' => $p));
    }


    /**
     * @Route("/addclassement", name="add_ranking")
     * Method({"GET","POST"})
     */

    public function AjouterEvenement(Request $request)
    {

        $c = new Classement();
        $form = $this->createForm(ClassementType::class, $c,  array(
            'event' => $request->get('id')));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $classements = $this->getDoctrine()->getRepository(Classement::class)->findBy(['evenement'=> $c->getEvenement()->getId()]);
            $ranks = [];
            foreach ($classements as $clas){
                array_push($ranks, $clas->getRang());
            }
            if(in_array($c->getRang(), $ranks)){
                $js= "Rank already exists";
                return $this->render('classement/ajouter_classement_admin.html.twig', array(
                    'classement' => $c, 'js'=>$js,
                    'form' => $form->createView(),
                ));
            } else {
                $js="";
            $em = $this->getDoctrine()->getManager();
            $em->persist($c);
            $em->flush();
            return $this->redirectToRoute('show_classement_admin');
            }
        }

        return $this->render('classement/ajouter_classement_admin.html.twig', array(
            'classement' => $c,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/deleteclassement/{id}", name="delete_classement")
     * Method({"GET","POST"})
     */
    public function DeleteClassement($id)
    {
        $em = $this->getDoctrine()->getManager();
        $c = $em->getRepository(Classement::class)->find($id);
        $em->remove($c);
        $em->flush();
        return $this->redirectToRoute('show_classement_admin');
    }

    /**
     * @Route("/editclassement/{id}", name="edit_classement")
     * Method({"GET","POST"})
     */
    public function ModifierAction(Request $request, Classement $c)
    {
        $previousRank = $c->getRang();
        $editForm = $this->createForm(EditClassementType::class, $c);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $newRank = $c->getRang();
            $em = $this->getDoctrine()->getManager();
            $old_c = new Classement();
            $old_c = $em->getRepository(Classement::class)->findOneBy(['rang'=>$newRank]);
            $em->remove($old_c);
            $old_c->setRang($previousRank);
            $em->persist($old_c);
            $em->flush();
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('show_classement_admin');
        }

        return $this->render('classement/modifier_classement_admin.html.twig', array(
            'classement' => $c,
            'form' => $editForm->createView(),
        ));
    }
}
