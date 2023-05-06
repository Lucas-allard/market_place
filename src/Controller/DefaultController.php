<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_default')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        dd($products);

        return $this->render('default/index.html.twig', [
            'products' => $products,
        ]);
    }
}
