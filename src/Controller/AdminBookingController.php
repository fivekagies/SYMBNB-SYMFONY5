<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings", name="admin_bookings_index")
     */
    public function index(BookingRepository $repo)
    {
        $bookings = $repo->findAll();

        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $bookings
        ]);
    }

    /**
     * @Route("/admin/bookings/{id}/edit", name="admin_bookings_edit")
     */
    public function edit(Booking $booking, EntityManagerInterface $manager, Request $request){
        $form = $this->createForm(AdminBookingType::class,$booking,[
            'validation_groups' => ["Default"]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //$booking->setAmount($booking->getAd()->getPrice() * $booking->getDuration());
            $booking->setAmount(0); //si amount empty Prepersist or preUpdate

            $manager->persist($booking); // on realité on a pas besoin de perssiter lors de la modification
                                            // car le manager à deja pris en compte la variable $booking
            $manager->flush();

            $this->addFlash(
                'success',
                "La réservation n° {$booking->getId()} a été bien modifiée !"
            );

            return $this->redirectToRoute("admin_bookings_index");
        }

        return $this->render("admin/booking/edit.html.twig",[
            "form" => $form->createView(),
            "booking" => $booking
        ]);
    }

    /**
     * @Route("/admin/bookings/{id}/delete", name="admin_bookings_delete")
     */
    public function delete(Booking $booking, EntityManagerInterface $manager){
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La reservation a bien été supprimer ! "
        );

        return $this->redirectToRoute("admin_bookings_index");
    }
}
