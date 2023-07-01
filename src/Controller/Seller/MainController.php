<?php

namespace App\Controller\Seller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_SELLER')]
#[Route('/ma-boutique', name: 'app_seller')]
class MainController extends AbstractController
{
    #[Route('/', name: '_index')]
    public function index(): Response
    {
        return $this->render('seller/main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
