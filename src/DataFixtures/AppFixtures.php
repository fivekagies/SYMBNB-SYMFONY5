<?php

namespace App\DataFixtures;

use App\Entity\Ad;
//use Cocur\Slugify\Slugify;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder=$encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker=Factory::create('fr-FR');
        //je crée un nouveau role
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        //puis je crée un utilisateur qui a le role admin
        $adminUser = new User();
        $adminUser->setFirstName("yassine")
            ->setLastName("knit")
            ->setEmail("yassine@mail.com")
            ->setHash($this->encoder->encodePassword($adminUser,"punch123"))
            ->setPicture('https://api.adorable.io/avatars/200/abott@adorable.png')
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
            ->addUserRole($adminRole);

        $manager->persist($adminUser);

        //$slugify=new Slugify();

        //gerer les utilisateurs
        $users=[];
        $genres=['male','female'];

        for($i=1;$i<=10;$i++){
            $user = new User();
            $genre = $faker->randomElement($genres);
            $picture='https://randomuser.me/api/portraits/';
            $pictureId=$faker->numberBetween(1,99);

            $picture .= ($genre=='male'? 'men/' : 'women/').$pictureId.'.jpg';

            $hash=$this->encoder->encodePassword($user,'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
                ->setHash($hash)
                ->setPicture($picture);

            $manager->persist($user);
            $users[]=$user;
        }


        //gerer les annonces
        for ($i=1;$i<=30;$i++)
        {
            $ad= new Ad();

            $title=$faker->sentence();
            //$slug=$slugify->slugify($title);
            $coverImage=$faker->imageUrl(1000,350);
            $introduction=$faker->paragraph(2);
            $content='<p>'.join('</p><p>',$faker->paragraphs(5)).'</p>';

            $user=$users[mt_rand(0,count($users)-1)];

            $ad->setTitle($title)
                //->setSlug($slug)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(mt_rand(40,200))
                ->setRooms(mt_rand(1,5))
                ->setAuthor($user);


            for ($j=1;$j<=mt_rand(2,5);$j++)
            {
                $image=new Image();
                $image->setCaption($faker->sentence())
                    ->setUrl($faker->imageUrl())
                    ->setAd($ad);
                $manager->persist($image);
            }
            // Gestion des réservations
            for($j=0;$j<mt_rand(0,10);$j++)
            {
                $booking = new Booking();

                $createdAt = $faker->dateTimeBetween('-6 months','-3 months');
                $startDate = $faker->dateTimeBetween('-3 months','now');

                //Gestion de la date de fin
                $duration = mt_rand(3,10);
                $endDate = (clone $startDate)->modify("+$duration days");
                $amount = $ad->getPrice() * $duration;
                $comment = $faker->paragraph();

                $booker = $users[mt_rand(0,count($users)-1)];

                $booking->setBooker($booker)
                    ->setAd($ad)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setCreatedAt($createdAt)
                    ->setAmount($amount)
                    ->setComment($comment)
                ;

                $manager->persist($booking);

                // les commentaires viennent aprés la reservation
                // Gestion des commentaires

                if(mt_rand(0,1)){
                    $comment = new Comment();

                    $comment->setContent($faker->paragraph)
                        ->setRating(mt_rand(1,5))
                        ->setAuthor($booker)
                        ->setAd($ad)
                        ;



                    $manager->persist($comment);
                }

            }



            // $product = new Product();
            // $manager->persist($product);
             $manager->persist($ad);
        }
        $manager->flush();
    }
}
