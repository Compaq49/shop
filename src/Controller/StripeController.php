<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(Cart $cart, EntityManagerInterface $entityManager, $reference): Response
    {
        $products_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        
        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
        
        if(!$order) {
            new JsonResponse(['error' => 'order']);
        }
        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object=$entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }
        // Seconde méthode
        /*foreach ($cart->getFull() as $product) {
            $products_for_stripe[] = [
                    'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product['product']->getIllustration()], 
                        ], 
                    ], 
                'quantity' => $product['quantity'], 
            ]; 
        }*/
        
        $products_for_stripe[] = [
                    'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $order->getCarrierPrice() * 100,
                    'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                  ],
                ],
                'quantity' => 1,
        ];
        
        Stripe::setApiKey('sk_test_51IHbm2JCknTJLH2E6q9yCYeDJHGPii6AcgRiczD3vgVRUiCYOFzybsrI3gJqskfaRWAQsj6rpNyuodUIKjvQncwg00fWW5r39S');
        
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
          'payment_method_types' => ['card'],
          'line_items' => [
              $products_for_stripe ],
          'mode' => 'payment',
          'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
          'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}', 
        ]);
        
        $order->setStripeSessionId($checkout_session->id);
        
        $this->entityManager->flush();
        
        $response = new JsonResponse(['id' => $checkout_session->id]); 
        return $response; 
    }
}
