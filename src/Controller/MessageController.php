<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;

use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/")
 */
class MessageController extends AbstractController
{
    /**
     * @Route("/dashboard/messages", name="app_message_dashboard", methods={"GET"})
     */
    public function dashboard(MessageRepository $messageRepository): Response
    {
        return $this->render('dashboard/messages.html.twig', [
            'messages' => $messageRepository->findAll(),
            
        ]);
    }

    /**
     * @Route("/message", name="app_message_index", methods={"GET"})
     */
    public function index(MessageRepository $messageRepository): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        return $this->render('message/index.html.twig', [
            'messages' => $messageRepository->findAll(),
            'form' => $form->createview(),
        ]);
    }

    /**
     * @Route("/message/new", name="app_message_new", methods={"GET", "POST"})
     */
    public function new(Request $request, MessageRepository $messageRepository): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        $message->setDate(new \DateTime('now'));
        $message->setSender($this->getUser());
        $message->setSeen(0);

        if ($form->isSubmitted() && $form->isValid()) {
            $messageRepository->add($message);
            return $this->render('message/index.html.twig', [
                'messages' => $messageRepository->findAll(),
                'form' => $form->createview(),
            ]);
        }

        return $this->render('message/index.html.twig', [
            'messages' => $messageRepository->findAll(),
            'form' => $form->createview(),
        ]);
    }

    /**
     * @Route("/reply/{receiver}", name="app_message_reply", methods={"GET", "POST"})
     */
    public function reply(Request $request, int $receiver, MessageRepository $messageRepository): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        $message->setDate(new \DateTime('now'));
        $message->setSender($this->getUser());
        $message->setSeen(0);
        $message->setReceiver($this->getDoctrine()
        ->getRepository(User::class)
        ->find(['id' => $receiver])) ;
       
        if ($form->isSubmitted() && $form->isValid()) {
            $messageRepository->add($message);
            return $this->render('message/reply.html.twig', [
                'messages' => $messageRepository->findMessages($receiver),
                'form' => $form->createView(),
            ]);
        }
        
        return $this->render('message/reply.html.twig', [
            'messages' => $messageRepository->findMessages($receiver),
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/message/{id}/edit", name="app_message_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Message $message, UserRepository $userRepository , MessageRepository $messageRepository): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $messageRepository->add($message);
            return $this->render('dashboard/messages.html.twig', [
                'messages' => $messageRepository->findAll(),
                'users' => $userRepository->findAll(),
            ]);
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
            'users' => $userRepository->findAll(),
        ]);
    }


     /**
     * @Route("/deleteAll/{receiver}", name="app_message_deleteall", methods={"POST", "GET"})
     */
    public function deleteAll(Request $request, int $receiver, MessageRepository $messageRepository): Response
    {
        $messageRepository->deleteAll($receiver);
        return $this->render('dashboard/messages.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/message/{id}", name="app_message_delete", methods={"POST"})
     */
    public function delete(Request $request, Message $message, MessageRepository $messageRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $messageRepository->remove($message);
        }

        return $this->render('dashboard/messages.html.twig', [
            'messages' => $messageRepository->findAll(),
        ]);
    }
}
