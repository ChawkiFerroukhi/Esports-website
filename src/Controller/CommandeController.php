<?php

namespace App\Controller;

use App\Entity\LigneCommande;
use App\Entity\User;
use App\Entity\Produit;
use App\Services\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormBuilderInterface;
use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Form\CommandeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Security\Core\Security;




class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="commande")
     */
    public function index(): Response
    {
        return $this->render('commande/index.html.twig', [
            'controller_name' => 'CommandeController',
        ]);
    }

    /**
     * @Route("/dashboard/orders", name="AfficheCommande")
     */
    public function AfficheCommande(){
        $repository=$this->getDoctrine()->getRepository(Commande::class);
        $commande=$repository->findAll();
        return $this->render('commande/AfficheC.html.twig',
            ['commande'=>$commande]);
    }

    /**
     * @Route("/SupprimerCommande/{id}", name="SupprimerCommande")
     */
    public function SupprimerCommande($id, CommandeRepository $repository, CartService $cartService){
        $commande=$repository->find($id);
        $em=$this->getDoctrine()->getManager();

        $prodsArr = $cartService->getFullCart();
        for ($i=1; $i<$commande->getQuantite(); $i++){
            $prodsArr[$i]["product"]->setQuantity($prodsArr[$i]["product"]->getQuantity() + 1);
        }


        $em->remove($commande);
        $em->flush();
        return $this->redirectToRoute('AfficheCommande');
    }

    /**
     * @Route("/AjouterCommande", name="AjouterCommande")
     */
    public function AjouterCommande(Request $request, CartService $cartService, \Swift_Mailer $mailer){
        $commande=new Commande();
        $em=$this->getDoctrine()->getManager();
        $commande->setDate(new \DateTime());
        $commande->setStatus('In Progress');
        $commande->setPrixTotal($cartService->getTotal());
        $commande->setQuantite($cartService->getQuantity());
        $commande->setUser($this->getUser());
        $prodsArr = $cartService->getFullCart();
        for ($i=0; $i<$commande->getQuantite(); $i++){
            $nouvLigne = new LigneCommande();
            $nouvLigne->setProduit($prodsArr[$i]["product"]);
            $nouvLigne->setQuantite($prodsArr[$i]["quantity"]);
            $nouvLigne->setCommande($commande);
            $em->persist($nouvLigne);
            $prodsArr[$i]["product"]->setQuantity($prodsArr[$i]["product"]->getQuantity() - 1);
        }


        $em->persist($commande);
        $em->flush();


        $prodsArr = $cartService->emptyCart();
        $currentUser = $this->security->getUser()->getUsername();
        $userRepository=$this->getDoctrine()->getRepository(User::class);
        $user=$userRepository->findOneBy(['username' => $currentUser]);




        $message = (new \Swift_Message('Merci Pour Votre Commande !'))
            ->setFrom('runtimeerrorlevelup@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'commande/mail.html.twig', [
                    'commande' => $commande
                ]),
                'text/html'
            )

            // you can remove the following code if you don't define a text version for your emails
            ->addPart(
                $this->renderView('commande/mail.html.twig', [
                    'commande' => $commande
                ]),
                'text/plain'
            )
        ;

        $mailer->send($message);


        return $this->redirectToRoute('commandeInvoice', ['id' => $commande->getId()]);

    }


    /**
     * @Route("/ModifierCommande/{id}", name="ModifierCommande")
     */
    function ModifierCommande(CommandeRepository $repository, $id, Request $request){
        $commande=$repository->find($id);
        $form=$this->createForm(CommandeType::class,$commande);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('AfficheCommande');
        }
        return $this->render('commande/ModifierC.html.twig',
            ['form'=>$form->createView()]);
    }

    /**
     * @Route("/commandeInvoice/{id}", name="commandeInvoice")
     */
    public function invoice($id)
    {
        $commande = $this->getDoctrine()->getRepository(Commande::class)
            ->find($id);
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('commande/invoice.html.twig', [
            'commande' => $commande
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("Invoice.pdf", [
            "Attachment" => false
        ]);
    }

    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

}