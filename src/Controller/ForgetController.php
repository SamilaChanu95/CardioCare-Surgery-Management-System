<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\User\ChangePasswordType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ForgetController extends AbstractController
{
    /**
     * @Route("/forget", name="forget")
     */
    public function forget(Request $request, UserPasswordEncoderInterface $encoder, UserRepository $userRepository)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $changePassword = $request->request->get('change_password');

        $username = $changePassword['username'];
        $password = $changePassword['plainPassword']['first'];

        $user       = $entityManager->getRepository(User::class)->findBy(['username' => $username]);
        $userInfo = ['username' => null, 'plainPassword' => null];

        if (!$user) {
            $this->addFlash('error', 'Please add username');
        }

        $form = $this->createForm(ChangePasswordType::class, $userInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('success-forget', 'Successfully change the Password!');

            $userInfo = $form->getData();
            $username = $userInfo['username'];
            $plainPassword = $userInfo['plainPassword'];

            $user = $userRepository->findOneBy(['username' => $username]);
            if ($user === null) {
                $this->addFlash('danger', 'Invalid username');
                return $this->redirectToRoute('forget');
            }
                $password = $encoder->encodePassword($user, $plainPassword);

                $user->setPassword($password);
                $userRepository->flush();

                return $this->redirectToRoute('signin');
            }
            return $this->render('forget/forget.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
