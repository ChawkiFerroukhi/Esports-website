<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Participants;
use App\Entity\Reclamation;
use App\Entity\User;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParticipantsController extends AbstractController
{
    /**
     * @Route("/add_participant/{id_user}/{id_event}", name="add_participants")
     */
    public function AjouterParticipants(Request $request): Response
    {
        $pars = new Participants();
        $idEvent = $request->get('id_event');
        $pars->setIdEvent($idEvent);
        $user =  $this->getDoctrine()->getRepository(User::class)->findBy(["id" => $request->get('id_user')])[0];
        $pars->setIdUser($user->getId());
        $em = $this->getDoctrine()->getManager();
        $em->persist($pars);
        $em->flush();
        return $this->redirectToRoute('event_details', ['id' => $idEvent,'id_user'=> $request->get('id_user')]);
    }

    /**
     * @Route("/get_qr", name="get_qr")
     */
    public function getQR(Request $request): Response
    {
        $writer = new PngWriter();
        // Create QR code
        $qrCode = QrCode::create('Data')
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

// Create generic logo
        $logo = Logo::create(__DIR__.'/../../public/front-office/images/'.$request->get("image"))
            ->setResizeToWidth(50);

// Create generic label
        $label = Label::create($request->get('label'))
            ->setTextColor(new Color(255, 0, 0));

        $result = $writer->write($qrCode, $logo, $label);
        $result->saveToFile(__DIR__.'/../../public/qr_codes/code.png');

        return $this->redirectToRoute("show_events");

    }


    /**
     * @Route("/annuler_participant/{id_part}/{id_user}", name="annuler_participants")
     */
    public function AnnulerParticipants(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $participants =  $em->getRepository(Participants::class)->find($request->get('id_part'));
        $idEvent = $participants->getIdEvent();
        $em->remove($participants);
        $em->flush();
        return $this->redirectToRoute('event_details',['id'=> $idEvent,'id_user'=>$request->get('id_user')]);
    }
}
