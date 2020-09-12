<?php

namespace App\Controller;

use App\Service\StatsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admin_dashboard")
     */
    public function index(EntityManagerInterface $manager, StatsService $statsService)
    {
        /*$users = $statsService->getUsersCount();
        $bookings = $statsService->getBookingsCount();
        $ads = $statsService->getAdsCount();
        $comments = $statsService->getCommentsCount();*/
        $stats = $statsService->getStats();

        $bestAds = $statsService->getAdsStats('DESC');

        $worstAds = $statsService->getAdsStats('ASC');

        return $this->render('admin/dashboard/index.html.twig',[
            'stats' => $stats,
            'bestAds' => $bestAds,
            'worstAds'=> $worstAds
        ]);
    }
}
