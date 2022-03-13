<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Bloglikes;
use App\Entity\Commentaire;
use App\Form\BlogType;
use App\Form\CommentaireType;
use App\Repository\BloglikesRepository;
use App\Repository\BlogRepository;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class BlogController extends AbstractController
{
    /**
     * @Route("/dashboard/blogs", name="blog_index", methods={"GET"})
     */
    public function dashboard(BlogRepository $blogRepository): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        return $this->render('dashboard/blogs.html.twig', [
            'blogs' => $blogRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/blogs", name="blogs", methods={"GET"})
     */
    public function index(BlogRepository $blogRepository): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        return $this->render('blog/index.html.twig', [
            'blogs' => $blogRepository->findAll(),
        ]);
    }

    /**
     * @Route("/blog/addblog", name="blog_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
        $blog->setpostDate(new \DateTime('now'));

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($blog);
            $entityManager->flush();

            return $this->redirectToRoute('blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show", methods={"GET", "POST"})
     */
    public function show(Blog $blog, Request $request, $id , EntityManagerInterface $entityManager,CommentaireRepository $commentaireRepository): Response
    {
        $commentaires = $commentaireRepository->findBy(array('blog'=>$id));
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setUser($this->getUser());
            $commentaire->setDate(new \DateTime("now"));
            $p = $entityManager->getRepository(Blog::class)->findBy(["id" => $request->get('id')])[0];
            $commentaire->setBlog($p);
            $entityManager->persist($commentaire);
            $entityManager->flush();

            return $this->redirectToRoute('blog_show', ["id"=>$request->get("id")], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
            'form' =>$form->createView(),
            'commentaire' => $commentaire,
            'commentaires' => $commentaires,
        ]);
    }

    /**
     * @Route("/{id}/blog_edit", name="blog_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete_blog/{id}", name="blog_delete", methods={"POST"})
     */
    public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('blog_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/blog/{id}/like", name="blog_like")
     */

    public function like(Blog $blog, EntityManagerInterface $entityManager, BloglikesRepository $bloglikesRepository) : Response 
    {
        $user = $this->getUser();

        if (!$user) return $this->json([
            'code' => 403,
            'message' => 'Unauthorized'
        ], 403);

        if ($blog->isLikedByUser($user)){
            $like = $bloglikesRepository->findOneBy([
                'blog' => $blog,
                'user' => $user
            ]);

            $entityManager->remove($like);
            $entityManager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Like deleted',
                'likes' => $bloglikesRepository->count(['blog' => $blog])
            ], 200);
        }

        $like = new Bloglikes();
        $like->setBlog($blog)
             ->setUser($user);

        $entityManager->persist($like);
        $entityManager->flush();

        return $this->json([
            'code' => 200,
            'message' => 'Like added',
            'likes' => $bloglikesRepository->count(['blog' => $blog])
        ], 200);
    }
}