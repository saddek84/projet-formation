<?php

namespace App\Controller;

use App\Entity\Formateur;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new Formateur();
        $user->setRoles(['ROLE_USER']);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $email=$user->getMail();
        dump($email);

        if ($form->isSubmitted() && $form->isValid() && $email->customAction()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            dump($user);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre compte à bien été enregistré.');
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'email' => $email,
        ]);
    }
    public function customAction()
    {
        $email = 'value_to_validate';
    

        $emailConstraint = new EmailConstraint();
        $emailConstraint->message = 'email invalid';

        $errors = $this->get('validator')->validateValue(
        $email,
        $emailConstraint 
    );

    // $errors is then empty if your email address is valid
    // it contains validation error message in case your email address is not valid
    // ...
    }
}
