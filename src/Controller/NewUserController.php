<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\DepartementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/dashboard")
 */

class NewUserController extends AbstractController
{
    /**
     * @Route("/users", name="new_user_index", methods={"GET", "POST"})
     */
    public function index(UserRepository $userRepository, DepartementRepository $departementRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        return $this->render('dashboard/index.html.twig', [
            'users' => $userRepository->findAll(),
            'departements' => $departementRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/register", name="new_user_new", methods={"GET", "POST"})
     * 
     */
    public function new(Request $request, EntityManagerInterface $entityManager , UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $user->setDateJoin(new \DateTime('now'));
        $user->setDepartement(Null);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $user->setPassword(
            $passwordEncoder->encodePassword($user, $user->getPassword()));
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('new_user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/adduser", name="new_user_admin", methods={"GET", "POST"})
     * 
     */
    public function newUser(Request $request, EntityManagerInterface $entityManager , UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $user->setDateJoin(new \DateTime('now'));

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $user->setPassword(
            $passwordEncoder->encodePassword($user, $user->getPassword()));
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('new_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('new_user/newuser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/profile", name="new_user_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(User $user): Response
    {
        return $this->render('new_user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edituser", name="edit_user_admin", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $request->isXmlHttpRequest()) {
            $entityManager->persist($user);
            $user->setPassword(
            $passwordEncoder->encodePassword($user, $user->getPassword()));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirect($this->generateUrl('new_user_index', array('id' => $user->getId())));
        }
        return $this->render('new_user/edituser.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="new_user_edit", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function editUser(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $user->setPassword(
            $passwordEncoder->encodePassword($user, $user->getPassword()));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('new_user_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('new_user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="new_user_delete", methods={"POST"}, requirements={"id":"\d+"})
     */
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('new_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
