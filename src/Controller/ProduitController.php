<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitController extends AbstractController
{
    /**
     * @Route("/store", name="store")
     */
    public function index(): Response
    {
        $produits= $this->getDoctrine()->getRepository(Produit::class)->findAll();
        return $this->render('produit/index.html.twig',['$produits'=> $produits]);
    }
    /*{
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
        ]);
    }*/

}