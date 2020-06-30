<?php
namespace App\Controller;

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
    public function home()
    {
        /*return new Response("<html>
                                       <head>
                                       <title>Mon Application</title>
</head> 
                                       <body>
                                       <h1 align='center'>Bonjour tout le monde</h1>
                                       <p>c'est mon premier page symfony hehe</p>
</body>
                                    </html>");*/
        $personnes=["yassine"=>22,"youssef"=>15,"david"=>20,"Jonatan"=>32];
        return $this->render("home.html.twig",[
            "title"=>"Ma premier page Symfony!",
            "age"=> 12,
            "personnes"=>$personnes

        ]);
    }
}

?>
