<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="app_user")
     */
    public function inscription(ManagerRegistry $doctrine, HttpFoundationRequest $requete, 
    UserPasswordEncoderInterface $encoder, SluggerInterface $slugger): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class,$user);
        $form->handleRequest($requete);
        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);

            // ajout d'une photo de profil
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('avatar')->getData();
            if($imageFile){
                $originalFileName = pathinfo($imageFile->getClientOriginalName(),
                PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName."-".uniqid().".".$imageFile->guessExtension();
                try {
                    $imageFile->move(
                        $this->getParameter('avatar'),
                        $newFileName
                    );
                } catch (FileException $error) {
                    $error->getMessage();
                }
                $user->setAvatar($newFileName);
            }
            $om = $doctrine->getManager();
            $om->persist($user);
            $om->flush();
            return $this->redirectToRoute('app_add_user');
        }
        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
