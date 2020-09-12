<?php
namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/hello/{prenom}/{age}",name="hello",requirements={"age"="\d+"})
     * @Route("/hello")
     * Afficher hello pour l'utilisateur
     *
     * @return Response
     */

    public function hello($prenom="",$age=0)
    {
        //return new Response("<p>Bonjour ".$prenom." a ".$age." ans</p>");
        return $this->render("hello.html.twig",[
            'prenom'=>$prenom,
            'age'=>$age
        ]);
    }


    /**
     * @Route("/",name="homepage")
     */
    public function home(AdRepository $adRepo,UserRepository $userRepo)
    {

        return $this->render("home.html.twig",[
            'ads' => $adRepo->findBestAds(3),
            'users' => $userRepo->findBestUsers(2)
        ]);
    }
}

?>
