<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo) //Injection de dependance (/ads => fournir auto un repositorie à index
    {
        //$repo = $this->getDoctrine()->getRepository(Ad::class);
        $ads=$repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * Permert de creer une annonce
     *
     * @Route("/ads/new",name="ads_create")
     * @IsGranted("ROLE_USER")
     *
     */
    public function create(Request $request, EntityManagerInterface $manager){
        $ad = new Ad();

        /*$image=new Image();
        $image->setUrl('http://placehold.it/400x200')
            ->setCaption('Titre 1');
        $ad->addImage($image);*/

        /*$form = $this->createFormBuilder($ad)
            ->add('title')
            ->add('introduction')
            ->add('content')
            ->add('rooms')
            ->add('price')
            ->add('coverImage')
            ->add('save', SubmitType::class, [
                'label' => 'Créer la nouvelle annonce',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ])
            ->getForm();*/

        //$request->request->get('title'); on peut recuperer les champs du formulaire de cette facon mais c un peu trop
                        //la fct HandleResquest() permet de parcourir la requete et d'extraire les infos du form

        $form=$this->createForm(AdType::class,$ad); //permet de creer un formulaire externe

        //la fct handle va relie les infos recuperer à notre objet $ad créé
        $form->handleRequest($request);
        //dump($ad);
        if($form->isSubmitted() && $form->isValid()) {
            //$manager = $this->getDoctrine()->getManager();
            foreach ($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée ! "
            );
            return $this->redirectToRoute('ads_show',[ 'slug' => $ad->getSlug() ]); //crée une Response qui demande une redirection sur une autre page
        }

        return $this->render('ad/new.html.twig',[
            "form" => $form->createView()
        ]);
    }


    /**
     * afficher une annonce
     *
     * @Route("/ads/{slug}",name="ads_show")
     */
    public  function show(Ad $ad){ // ad dont le slug est dans la route à l'aide de ParamConverter
        //$ad=$repo->findOneBySlug($slug);

        return $this->render("ad/show.html.twig", [
            "ad" => $ad
        ]);
    }
    /*public  function show($slug,AdRepository $repo){
        $ad=$repo->findOneBySlug($slug);

        return $this->render("ad/show.html.twig", [
            "ad" => $ad
            ]);
     }*/

    /**
     * Permet d'afficher le formulaire d'edition
     * @Route("ads/{slug}/edit",name="ads_edit")
     *
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas, vous pouvez pas la modifier")
     *
     */
    public function edit(Ad $ad,Request $request,EntityManagerInterface $manager){

        $form=$this->createForm(AdType::class,$ad); //permet de creer un formulaire externe

        //la fct handle va relie les infos recuperer à notre objet $ad créé
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            //$manager = $this->getDoctrine()->getManager();
            foreach ($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                'success',
                "Les modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées ! "
            );
            return $this->redirectToRoute('ads_show',[ 'slug' => $ad->getSlug() ]); //crée une Response qui demande une redirection sur une autre page
        }

        return $this->render("ad/edit.html.twig",[
            "form" => $form->createView(),
            "ad" => $ad
        ]);
    }

    /**
     * Permet de supprimer une annonce
     *
     * @Route("/ads/{slug}/delete", name="ads_delete" )
     *
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Vous n'avez pas le droit d'acceder à cette ressource")
     */
    public function delete(Ad $ad,EntityManagerInterface $manager)
    {
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
          'succes',
          "L'annonce <strong>{$ad->getTitle()}</strong> à bien été supprimée !"
        );

        return $this->redirectToRoute('ads_index');
    }
}
