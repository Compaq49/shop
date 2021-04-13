<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $products = $this->entityManager->getRepository(\App\Entity\Product::class)->findByIsBest(1);
        
        $headers = $this->entityManager->getRepository(\App\Entity\Header::class)->findAll();
        
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'headers' => $headers,
        ]);
    }
}
