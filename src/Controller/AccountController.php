<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gerer le formulaire de connexion
     *
     * @Route("/login", name="account_login")
     *
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig',[
            'hasError' => $error != null,
            'username' => $username
        ]);
    }

    /**
     * Permet de se deconnecter
     *
     * @Route("/logout",name="account_logout")
     *
     * @return void
     */
    public function logout(){
        //rien...!
    }

    /**
     * Peremet d'afficher le formulaire d'inscription
     *
     * @Route("/register", name="account_register")
     *
     * @return Response
     */
    public function register(Request $request,EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder){
        $user = new User();
        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $hash=$encoder->encodePassword($user,$user->getHash());
            $user->setHash($hash);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte à été bien crée ! Vous pouvez maintenant se connecter !"
            );

            return $this->redirectToRoute("account_login");
        }

        return $this->render('account/registration.html.twig',[
            "form" => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher et de traiter le formulaire de modification de profile
     *
     * @Route("/account/profile",name="account_profile")
     *
     * @return Response
     */
    public function profile(Request $request,EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $form=$this->createForm(AccountType::class,$user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                "success",
                "Les données de profil ont été enregistrée avec succés !"
            );
        }

        return $this->render("account/profile.html.twig",[
            "form" => $form->createView()
        ]);
    }

    /**
     * Permet de modifier le mot de passe
     *
     * @Route("/account/password-update",name="account_password")
     * @return Response
     */
    public function updatePassword(Request $request,UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        $passwordUpdate = new PasswordUpdate();
        $user=$this->getUser();

        $form=$this->createForm(PasswordUpdateType::class,$passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            //1 verifier que le old password du formulaire est le meme password de l'utilisateur
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getHash()))
            {
                //gerer l'erreur
                //on va recuperer le champ oldPassword du formulaire (de type Form) pour lui ajouter l'erreur avec addError()

                $form->get('oldPassword')->addError(new FormError("le mot de passe que vous avez tapé n'est pas 
                votre mot de passe actuel !"));
            }
            else
            {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user,$newPassword);

                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    "Votre mot de passe a bien été modifié !"
                );

                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render("account/password.html.twig",[
            "form" => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisateur connecté
     *
     * @Route("/account",name="account_index")
     *
     * @return Response
     */
    public function myAccount(){
        return $this->render("user/index.html.twig",[
            'user' => $this->getUser()
        ]);
    }
}
