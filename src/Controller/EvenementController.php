<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Participants;
use App\Entity\User;
use App\Repository\EvenementRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use src\Form\EditEvenementType;
use src\Form\EvenementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


class EvenementController extends AbstractController
{
    /**
     * @Route("/events", name="show_events")
     * Method({"GET"})
     */
    public function getEvents()
    {
        $p = $this->getDoctrine()->getRepository(Evenement::class)->findAll();
        return $this->render('/evenement/index.html.twig',
            array('events' => $p));
    }

    /**
     * @Route("/eventdetails/{id}/{id_user}", name="event_details")
     * Method({"GET"})
     */
    public function getEventDetails(Request $request)
    {
        $p = $this->getDoctrine()->getRepository(Evenement::class)->findBy(["id"=> $request->get('id')]);
        $id_event = $request->get('id');
        $par = $this->getDoctrine()->getRepository(Participants::class)->findObject($request->get('id_user'),  $id_event);

        if($par) {
            $idUsers = $par[0]->getIdUser();

            $idParticipation = $par[0]->getId();
            return $this->render('/evenement/event_details.html.twig',
                array('event' => $p[0], 'users'=> $idUsers, 'participation' => $idParticipation));
        }


        return $this->render('/evenement/event_details.html.twig',
            array('event' => $p[0], 'users'=> 0));
    }



//    /**
//     * @Route("/calendar", name="gotocalendar")
//     * Method({"GET"})
//     */
//    public function gotoCalendar()
//    {
//
//        $request = $this->get('request_stack')->getMasterRequest();
//
//        $googleCalendar = new GoogleCalendar();
//
//        $googleCalendar->setRedirectUri("http://127.0.0.1:8000/events");
//
//        if ($request->query->has('code') && $request->get('code')) {
//           $client = $googleCalendar->getClient($request->get('code'));
//        } else {
//
//           $client = $googleCalendar->getClient();
//
//        }
//
//        if (is_string($client)) {
//            return new RedirectResponse($client);
//        }
//
//        $events = $googleCalendar->getEventsForDate('primary', new \DateTime('now'));
//
//    }


    /**
     * @Route("/admin_events", name="show_events_admin")
     * Method({"GET"})
     */
    public function getEventsAdmin()
    {
        $p = $this->getDoctrine()->getRepository(Evenement::class)->findAll();
        return $this->render('evenement/afficher_evenements_admin.html.twig',
            array('events' => $p));
    }

    /**
     * @Route("/dashboard/admin_events_ordered", name="show_events_ordered_admin")
     * Method({"GET"})
     */
    public function getEventsAdminOrdered()
    {
        $value = 1;
        $p = $this->getDoctrine()->getRepository(Evenement::class)->findObject($value);
        return $this->render('evenement/afficher_evenement_tri_admin.html.twig',
            array('events' => $p));
    }


    /**
     * @Route("/addevent", name="add_events")
     * Method({"POST"})
     */

    public function AjouterEvenement(Request $request)
    {
        $event = new Evenement();
        $form = $this->createForm(EvenementType::class, $event);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $event->upload();
            $em->persist($event);
            $em->flush();
            return $this->redirectToRoute('show_events_admin');
        }

        return $this->render('evenement/ajouter_evenements_admin.html.twig', array(
            'event' => $event,
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/deleteevent/{id}", name="delete_event")
     * Method({"GET","POST"})
     */
    public function DeleteEvent($id,  LoggerInterface $logger)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository(Evenement::class)->find($id);
        $part = $em->getRepository(Participants::class)->findBy(['id_event'=> $event->getId()]);
        $em->remove($event);
        $em->remove($part[0]);
         $em->flush();
        $participants = $em->getRepository(Participants::class)->findBy(['id_event' => $id]);
        $users = [];
        foreach ($participants as $row) {
            $row = $em->getRepository(User::class)->find($row->getIdUser());
            array_push($users, $row->getEmail());
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->SMTPKeepAlive = true; //SMTP connection will not close after each email sent, reduces SMTP overhead
            $mail->Username   = 'beyaghalia.necib@esprit.tn';                     //SMTP username
            $mail->Password   = 'bayancib2000';                          //Enable implicit TLS encryption
            $mail->Port       = 25;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Event Canceled';
            $mail->Body    = 'An event which you RSVPed has been canceled. :)';
            $mail->AltBody = 'Event has been canceled.';
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            //Recipients
            $mail->setFrom('beyaghalia.necib@esprit.tn', 'RuntimeError E-Sports');

            foreach ($users as $row) {
                try {
                    $mail->addAddress($row);
                } catch (Exception $e) {
                    echo 'Invalid address skipped: ' . htmlspecialchars($row['email']) . '<br>';
                    continue;//Add a recipient
                }


                //Attachments
                try {
                    $mail->send();

                } catch (Exception $e) {
                    echo 'Mailer Error (' . htmlspecialchars($row['email']) . ') ' . $mail->ErrorInfo . '<br>';
                    //Reset the connection to abort sending this message
                    //The loop will continue trying to send to the rest of the list
                    $mail->getSMTPInstance()->reset();
                }
            }
            var_dump('Message has been sent');
        } catch (Exception $e) {
            var_dump("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");

        }
        $mail->clearAddresses();

        return $this->redirectToRoute('show_events_admin');
    }

    /**
     * @Route("/editevent/{id}", name="edit_event")
     * Method({"GET","POST"})
     */
    public function ModifierAction(Request $request, Evenement $event)
    {
        $editForm = $this->createForm(EditEvenementType::class, $event);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('show_events_admin');
        }

        return $this->render('evenement/modifier_evenements_admin.html.twig', array(
            'event' => $event,
            'form' => $editForm->createView(),
        ));
    }

    /**
     * @Route("/show_calendar", name="show_calendar")
     * Method({"GET","POST"})
     */
    public function ShowCalendar( Request $request, EvenementRepository $eventRepo,   LoggerInterface $logger) : Response
    {

        $res = [];

        $events = $this->getDoctrine()->getRepository(Evenement::class)->findAll();
        foreach($events as $event)
        {
            $res[] = [
                'id'=> $event->getId(),
                'start'=> $event->getDate()->format('Y-m-d H:i:s'),
                'end'=> $event->getDate()->format('Y-m-d H:i:s'),
                'title'=> $event->getNom(),
            ];
        }

        $data = json_encode($res);

        return $this->render('evenement/afficher_calendrier_admin.html.twig', [
            'data'=>$data,

        ]);

    }
    /**
     * @Route("/edit_calendar/{id}", name="edit_calendar")
     * Method({"GET","PUT"})
     */
    public function majEvent(?Evenement $event, Request $request,EntityManagerInterface $entityManager): Response
    { // On récupère les données
        $donnees = json_decode($request->getContent());
        if
        (isset($donnees->title) && !empty($donnees->title) && isset($donnees->start) && !empty($donnees->start) )

        {  //les donnees sont completes
            $code=200;
            if(!$event)
            {

                $event=new $event;
                $code=201;

            }
            $event->setDate(new DateTime($donnees->start));
            $event->setNom($donnees->title);

            if($donnees->allDay){
                $event->setDate(new DateTime($donnees->start));
            }
            else {
                $event->setDate(new DateTime($donnees->start));
            }
            $entityManager->persist($event);
            $entityManager->flush();
            return new Response('OK', $code);
        }

        else {
            // Les données sont incomplètes
            return new Response('données incomplete', 404);
        }
    }

}