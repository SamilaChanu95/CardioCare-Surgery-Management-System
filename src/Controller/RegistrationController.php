<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="registration")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form -> handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $plainPassword = $user -> getPlainPassword();

            $encoded = $encoder -> encodePassword($user, $plainPassword);

            $user->setPassword($encoded);

            $em = $this->getDoctrine()->getManager();
 
            $em->persist($user);

            $em->flush();

            $this->addFlash('success', 'You are now successfully registered!');

            return $this->redirectToRoute('home');
        }
        return $this->render('registration/register.html.twig', [
            'registration_form' => $form->createView(),
        ]);
    }
}
