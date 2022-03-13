<?php

namespace App\Controller;

use App\Entity\ProfilePosts;
use App\Form\ProfilePostsType;
use App\Repository\ProfilePostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class ProfilePostsController extends AbstractController
{
    /**
     * @Route("/posts", name="profile_posts_index", methods={"GET"})
     */
    public function index(ProfilePostsRepository $profilePostsRepository): Response
    {
        return $this->render('profile_posts/index.html.twig', [
            'profile_posts' => $profilePostsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/posts/new", name="profile_posts_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ProfilePostsRepository $profilePostsRepository, EntityManagerInterface $entityManager): Response
    {
        $profilePost = new ProfilePosts();
        $form = $this->createForm(ProfilePostsType::class, $profilePost);
        $form->handleRequest($request);
        
        if ($request->isXmlHttpRequest()) {

            if ($form->isSubmitted() && $form->isValid()) {
                $profilePost->setUser($this->getUser());
                $profilePost->setImage('bg1.jpg');
                $entityManager->persist($profilePost);
                $entityManager->flush();
                return $this->json([
                    'code' => 200,
                    'message' => 'Post Added',
                    'content' => $profilePost->getContent(),
                ], 200);
                
            
            }
        }
    }

    /**
     * @Route("/profilepost/{id}", name="profile_posts_show", methods={"GET"})
     */
    public function show(ProfilePosts $profilePost): Response
    {
        return $this->render('profile_posts/show.html.twig', [
            'profile_post' => $profilePost,
        ]);
    }

    /**
     * @Route("/profilepost/{id}/edit", name="profile_posts_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, ProfilePosts $profilePost, ProfilePostsRepository $profilePostsRepository): Response
    {
        $form = $this->createForm(ProfilePostsType::class, $profilePost);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePostsRepository->add($profilePost);
            return $this->redirectToRoute('profile_posts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile_posts/edit.html.twig', [
            'profile_post' => $profilePost,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profilepost/{id}", name="profile_posts_delete", methods={"POST"})
     */
    public function delete(Request $request, ProfilePosts $profilePost, ProfilePostsRepository $profilePostsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$profilePost->getId(), $request->request->get('_token'))) {
            $profilePostsRepository->remove($profilePost);
        }

        return $this->redirectToRoute('profile_posts_index', [], Response::HTTP_SEE_OTHER);
    }
}
