<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(ArticleRepository $repoArticle): Response
    {
        $cellules = $repoArticle->findAll();

        return $this->render('main/index.html.twig', compact("cellules"));
    }

    #[Route('/show/{slug}', name: 'app_show')]
    public function show(Article $article): Response
    {
        return $this->render('main/show.html.twig', compact("article"));
    }
}
