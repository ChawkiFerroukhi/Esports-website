<?php

namespace App\Controller;

use App\Entity\ProfilePosts;
use App\Entity\User;
use App\Form\ProfilePostsType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\DepartementRepository;
use App\Repository\ProfilePostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/")
 */

class NewUserController extends AbstractController
{
    /**
     * @Route("/dashboard/users", name="new_user_index", methods={"GET", "POST"})
     */
    public function index(UserRepository $userRepository, DepartementRepository $departementRepository): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        return $this->render('dashboard/users.html.twig', [
            'users' => $userRepository->findBy(['departement' => NULL]),
            'departements' => $departementRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/dashboard/admins", name="admin_index", methods={"GET", "POST"})
     */
    public function admins(UserRepository $userRepository, DepartementRepository $departementRepository): Response
    {
        $user = new User();
        return $this->render('dashboard/admins.html.twig', [
            'users' => $userRepository->findAdmins($user->getDepartement() == NUll),
            'departements' => $departementRepository->findAll(),
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
        $user->setBanned(0);
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
     * @Route("/{id}/profile", name="new_user_show", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function show(Request $request, User $user, UserRepository $userRepository, ProfilePostsRepository $profilePostsRepository, EntityManagerInterface $entityManager): Response
    {

        $profilePost = new ProfilePosts();
        $form = $this->createForm(ProfilePostsType::class, $profilePost);
        $form->handleRequest($request);

        if ($request->isXmlHttpRequest()) {

            if ($form->isSubmitted() && $form->isValid()) {
                
                $profilePost->setUser($this->getUser());
                $profilePost->setImage('bg1.jpg');
                $profilePostsRepository->add($profilePost);
                return $this->json([
                    'code' => 200,
                    'message' => 'Post added',
                    'content' => $profilePost->getContent(),
                ], 200);
            }
        }

        return $this->render('new_user/show.html.twig', [
            'form' => $form->createView(),
            'profilepost' => $profilePost,
            'profileposts' => $profilePostsRepository->findBy(['user' => $this->getUser()]),
            'user' => $user,
            'users' => $userRepository->findAllExceptThis($this->getUser()),
        ]);
    }

    /**
     * @Route("/user/{id}/edit", name="edit_user_admin", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, DepartementRepository $departementRepository, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $user->setPassword(
            $passwordEncoder->encodePassword($user, $user->getPassword()));
            if ($user->getDepartement() != NULL) {
                $user->setRoles(['ROLE_ADMIN']);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('new_user_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('new_user/edituser.html.twig', [
            'user' => $user,
            'departements' => $departementRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}/ban", name="ban_user", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function BanUser(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        if ($user->getBanned() == 0)
        {
            $user->setBanned(1);
        }
        else {
            $user->setBanned(0);
        }
        $entityManager->flush();
        return $this->redirectToRoute('new_user_index', [], Response::HTTP_SEE_OTHER);

    }

    /**
     * @Route("/user/{id}/remove", name="remove_admin", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function RemoveAdmin(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {

        $user->setRoles(['ROLE_USER']);
        $user->setDepartement(NULL);


        $entityManager->flush();
        return $this->redirectToRoute('new_user_index', [], Response::HTTP_SEE_OTHER);

    }

    /**
     * @Route("/{id}/edit", name="new_user_edit", methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function editUser(Request $request, User $user, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($this->getUser() != $user){
            return $this->redirectToRoute('new_user_show', ['id' => $user->getId()]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $user->setPassword(
            $passwordEncoder->encodePassword($user, $user->getPassword()));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('new_user_show', ['id' => $user->getId()]);
        }
        return $this->render('new_user/edit.html.twig', [
            'user' => $user,
            'users' => $userRepository->findAllExceptThis($this->getUser()),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="delete_user_admin", methods={"POST"}, requirements={"id":"\d+"})
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
