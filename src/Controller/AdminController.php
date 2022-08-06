<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    ######################################### USER #################################################
    #[Route('/user', name: 'user')]
    #[Route('/user/{id}/delete', name: 'user_delete')]
    public function adminUser(EntityManagerInterface $manager, UserRepository $repoUser, User $userDelete = null): Response
    {
        $cellules = $repoUser->findAll();

        if($userDelete)
        {
            $id = $userDelete->getId();
            $manager->remove($userDelete);
            $manager->flush();

            $this->addFlash('success', "L'Utilisateur n°$id a bien été supprimé avec succès");
            return $this->redirectToRoute('app_admin_user');
        }
        return $this->render('admin/admin_user.html.twig', [
            'cellules' => $cellules,
        ]);
    }

    #[Route('/user/{id}/edit', name: 'user_update')]
    public function adminUserForm(User $user, Request $request, EntityManagerInterface $manager): Response
    {
 
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'userBack' => true 
        ]);

        $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {         
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', "La modification a bien été prise en compte !");

                return $this->redirectToRoute('app_admin_user');         
            
            }        

        return $this->render('admin/admin_user_form.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    ######################################### ARTICLE #################################################
    #[Route('/article', name: 'article')]
    #[Route('/article/{id}/delete', name: 'article_delete')]
    public function adminArticle(EntityManagerInterface $manager, ArticleRepository $repoArticle, Article $articleDelete = null): Response
    {
        $colonnes = $manager->getclassMetadata(Article::class)->getFieldNames();
        $cellules = $repoArticle->findAll();

        if($articleDelete)
        {
            $id = $articleDelete->getId();
            $manager->remove($articleDelete);
            $manager->flush();

            $this->addFlash('success', "L' article n°$id a bien été supprimé avec succès");
            return $this->redirectToRoute('app_admin_article');
        }
        return $this->render('admin/admin_article.html.twig', [
            'cellules' => $cellules,
            'colonnes' => $colonnes
        ]);
    }


    #[Route('/article/add', name: 'article_add')]
    #[Route('/article/{id}/edit', name: 'article_update')]
    public function adminarticleForm(Article $article = null, Request $request, EntityManagerInterface $manager, SluggerInterface $slugger): Response
    {
        if($article)
        {
            $photoActuelle = $article->getCover();

            if($article->getCover() == Null){
                $article->setCover('null');
            }

        }
        if(!$article)
        {
            $article = new Article;
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!$article->getId()){
                $txt = "enregistré";
                $leSlug = $form->get('title')->getData();
                $article->setSlug($slugger->slug($leSlug));
            } else {
                $txt = "modifier";
            }
            $cover = $form->get('cover')->getData();
            if($cover)
            {
                $nomOriginePhoto = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
                $nouveauNomFichier = $nomOriginePhoto . "-" . uniqid() . '.' . $cover->guessExtension();              
                try
                {
                    $cover->move(
                        $this->getParameter('photo_directory'),
                        $nouveauNomFichier
                    );
                }
                catch(FileException $e)
                {

                }
                $article->setCover($nouveauNomFichier);
            } else {
                if(isset($photoActuelle))
                    $article->setCover($photoActuelle);
                else
                    $article->setCover("null");
            }

            $article->setDateCreated(new \DateTimeImmutable('Europe/Paris'));
            $manager->persist($article);
            $manager->flush();

            $this->addFlash('success', "L'article a été $txt avec succès !");
 
            return $this->redirectToRoute('app_admin_article');
                
        }

        return $this->render('admin/admin_article_form.html.twig', [
            'form' => $form->createView(),
            'editMode' => $article->getId(),
            'article' => $article
        ]);
    }
}
