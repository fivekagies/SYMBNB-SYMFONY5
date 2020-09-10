<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PaginationService {

    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    public function __construct(EntityManagerInterface $manager, Environment $twig,RequestStack $request,$templatePath)
    {

        $this->manager = $manager;
        $this->twig    = $twig;
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->templatePath = $templatePath;
    }

    public function display(){

        $this->twig->display($this->templatePath,[
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    public function getPages(){
        if(empty($this->entityClass)){
            throw new \Exception("Vous n'avez pas spécifier l'entité sur lequelle nous devons paginer !
            Utilier la méthode setEntityClass() de votre objet PaginationService !");
        }
        //1 connaitre le total des enregistrements de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        //2 Faire la division, l'arrondie et le renvoyer
        $pages = ceil($total / $this->limit);


        return $pages;
    }

    public function getData(){
        if(empty($this->entityClass)){
            throw new \Exception("Vous n'avez pas spécifier l'entité sur lequelle nous devons paginer !
            Utilier la méthode setEntityClass() de votre objet PaginationService !");
        }
        //1 calculer l'offset
        $offset = ($this->currentPage - 1) * $this->limit;

        //2 trouver le repository pour pouvoir appeler la fct findBy
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([],[], $this->limit, $offset);

        //3 Renvoyer les elements en question
        return $data;
    }

    /**
     * @return mixed
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * @param mixed $trmplatePath
     */
    public function setTemplatePath($templatePath): void
    {
        $this->templatePath = $templatePath;
    }

    /**
     * @return mixed
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param mixed $route
     */
    public function setRoute($route): void
    {
        $this->route = $route;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->currentPage;
    }

    /**
     * @param int $currentPage
     */
    public function setPage(int $currentPage): void
    {
        $this->currentPage = $currentPage;
    }
    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return mixed
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param mixed $entityClass
     */
    public function setEntityClass($entityClass): void
    {
        $this->entityClass = $entityClass;
    }
}