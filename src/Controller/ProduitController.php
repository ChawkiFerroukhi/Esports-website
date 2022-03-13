<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Vich\UploaderBundle\Form\Type\VichImageType;


class ProduitController extends AbstractController
{

    /**
     * @Route("/store", name="store")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $donnees = $this->getDoctrine()->getRepository(Produit::class)->findBy(['active' => 'true']);
        $produits = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('produit/index.html.twig', ['produits' => $produits]);
    }

    /**
     * @Route("/store/TriAsc", name="storeTriCroi")
     */
    public function triPrixCroissant(Request $request, PaginatorInterface $paginator)
    {
        $donnees = $this->getDoctrine()->getRepository(Produit::class)->findBy(['active' => 'true'], array('price' => 'ASC'));
        $produits = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('produit/index.html.twig', ['produits' => $produits]);
    }

    /**
     * @Route("/store/TriDesc", name="storeTriDecroi")
     */
    public function triPrixDecroissant(Request $request, PaginatorInterface $paginator)
    {
        $donnees = $this->getDoctrine()->getRepository(Produit::class)->findBy(['active' => 'true'], array('price' => 'DESC'));
        $produits = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('produit/index.html.twig', ['produits' => $produits]);
    }

    /**
     * @Route("/store/QuantityAsc", name="QuantityAsc")
     */
    public function QuantityAsc(Request $request, PaginatorInterface $paginator)
    {
        $donnees = $this->getDoctrine()->getRepository(Produit::class)->findBy(['active' => 'true'], array('quantity' => 'ASC'));
        $produits = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('produit/index.html.twig', ['produits' => $produits]);
    }

    /**
     * @Route("/store/QuantityDesc", name="QuantityDesc")
     */
    public function QuantityDesc(Request $request, PaginatorInterface $paginator)
    {
        $donnees = $this->getDoctrine()->getRepository(Produit::class)->findBy(['active' => 'true'], array('quantity' => 'DESC'));
        $produits = $paginator->paginate(
            $donnees,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('produit/index.html.twig', ['produits' => $produits]);
    }

    /**
     * @Route("/dashboard/produits", name="all_produit")
     */
    public function showAll(): Response
    {
        $produits = $this->getDoctrine()->getRepository(Produit::class)->findAll();
        return $this->render('produit/all.html.twig', ['produits' => $produits]);
    }

    /**
     * @Route("dashboard/addProduit", name="new_produit")
     * Method({"GET", "POST"})
     */
    public function new(Request $request) {
        $Produit = new Produit();
        $form = $this->createForm(ProduitType::class,$Produit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $Produit = $form->getData();
            if($Produit->getSolde() != null){ $Produit->setPrice($Produit->getPrice() -( $Produit->getPrice() * $Produit->getSolde() /100) ) ; }
            $Produit->setUpdatedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($Produit);
            $entityManager->flush();

            return $this->redirectToRoute('new_produit');
        }
        $produits= $this->getDoctrine()->getRepository(Produit::class)->findAll();
        return $this->render('produit/new.html.twig',['form' => $form->createView(),'produits'=> $produits]);

    }

    /**
     * @Route("/store/produit/{id}", name="produit_show")
     */
    public function show($id) {
        $produit = $this->getDoctrine()->getRepository(Produit::class)
            ->find($id);
        if ($produit->getActive() == true)
        {
            return $this->render('produit/show.html.twig',
                array('produit' => $produit));
        }
        else{
            return $this->render('home/404.html.twig',
                array('produit' => $produit));
        }
    }


    /**
     * @Route("/dashboard/produit/edit/{id}", name="edit_produit")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id) {
        $produit = new Produit();
        $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);

        $form = $this->createForm(ProduitType::class,$produit);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {

            if($produit->getSolde() != null){ $produit->setPrice($produit->getPrice() -( $produit->getPrice() * $produit->getSolde() /100) ) ; }
            $produit->setUpdatedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('all_produit');
        }
        return $this->render('produit/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/dashboard/produit/delete/{id}",name="delete_produit")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id) {
        $produit = $this->getDoctrine()->getRepository(Produit::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($produit);
        $entityManager->flush();

        $response = new Response();
        $response->send();
        return $this->redirectToRoute('new_produit');
    }

    /**
     * @Route("/store/produit/{id}", name="produit_det")
     */
    public function detail($id) {
        $produit = $this->getDoctrine()->getRepository(Produit::class)
            ->find($id);
        if ($produit->getActive() == true)
        {
            if ($produit->getQuantity() != 0){
                return $this->render('produit/show.html.twig',
                    array('produit' => $produit));
            }
            else{
                return $this->render('home/404.html.twig',
                    array('produit' => $produit));
            }
        }
        else{
            return $this->render('home/404.html.twig',
                array('produit' => $produit));
        }

    }

}