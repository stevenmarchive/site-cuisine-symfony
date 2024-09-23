<?php

namespace App\Controller;

// Importation des classes nécessaires pour le controller
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

	#[Route('/', 'accueil.index', methods: ['GET'])]
	public function index(): Response
	{

		// Connecte le controller avec le template
		return $this->render('page/accueil.html.twig');
	}
}
