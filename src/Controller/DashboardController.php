<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use App\Repository\CommandeRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    /**
    * @Route("/dashboard", name="dashboard")
    */
    public function index(UserRepository $userRepository, CommandeRepository $commandeRepository, ProduitRepository $produitRepository): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
            'users' => $userRepository->findBy([ 'banned' => '0']),
            'bannedusers' => $userRepository->findBy([ 'banned' => '1']),
            'commandes' => $commandeRepository->findAll(),
            'produits' => $produitRepository->findBy(['active' => 'true']),
        ]);
    }
}
