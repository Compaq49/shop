<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }     

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {   
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        
        if(!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        // Vider la session cart
        $cart->remove();
        // Modifier le statut Stata de notre commande en la passant à 1 
        if($order->getState() == 0) {
            $order->setState(1);
            $this->entityManager->flush();
            
            $mail = new Mail();
            $content = "Bonjour ".$order->getUser()->getFirstname().','."<br/>Bienvenue sur la première boutique dédiée au made in France.<br><br/>Merci pour votre commande.";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Votre commande sur La Boutique Française est bien validée.', $content);
        }
        
        return $this->render('order_success/index.html.twig', [
            'order' => $order,
        ]);
    }
}
