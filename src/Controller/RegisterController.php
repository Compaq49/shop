<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager) 
    {
        $this->entityManager = $entityManager;
    }
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
            
            if(!$search_email) {
                $passwordCrypte = $encoder->encodePassword($user, $user->getPassword());
                $user->setPassword($passwordCrypte);
                
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                
                $this->addFlash('success', 'Inscription validée. Vous pouvez vous connecter.');
                return $this->redirectToRoute('home');

            } else {
                $this->addFlash('danger', "Cet email est déjà utilisé, entrez un email valide.");
                return $this->redirectToRoute('register');

            }
        }
        
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
