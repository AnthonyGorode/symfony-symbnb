<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Booking;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

         $adminUser = new User();
         $adminUser->setFirstName('Anthony')
                   ->setLastName('Gorode')
                   ->setEmail('anthony.gorode@gmail.com')
                   ->setHash($this->encoder->encodePassword($adminUser,'password'))
                   ->setPicture('https://scontent-yyz1-1.cdninstagram.com/vp/9c278fb82c700243cf2ab9391a9d2a55/5D0CC817/t51.2885-19/s150x150/47448280_724832411231066_2597987633875386368_n.jpg?_nc_ht=scontent-yyz1-1.cdninstagram.com&se=8')
                   ->setIntroduction($faker->sentence())
                   ->setDescription('<p>'.join('</p><p>', $faker->paragraphs(3)) . '</p>')
                   ->addUserRole($adminRole);
        $manager->persist($adminUser);


        //Nous gérons les utilisateurs
        $users=[];
        $genres=['male','female'];

        for($i=1; $i <=10; $i++){
            $user = new User();

            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1,99) . '.jpg';

            // condition ternaire
            $picture .=($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            // condition classique correspondante
            // if($genre == 'male') $picture = $picture . 'men/' . $picutreId;
            // else $picture = $picture . 'women/' . $pictureId;

            $hash = $this->encoder->encodePassword($user,'password');

            $user->setFirstName($faker->firstname($genre))
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setIntroduction($faker->sentence())
                 ->setDescription('<p>'.join('<p></p>',$faker->paragraphs(3)) .'</p>')
                 ->setHash($hash)
                 ->setPicture($picture);

            $manager->persist($user);
            $users[] = $user;
        }

        // Nous gérons les annonces
        for ($i=1;$i<=30;$i++){

            $title = $faker->sentence();
            $coverImage = $faker->imageUrl(1000,350);
            $introduction = $faker->paragraph(2);
            $contain = '<p>'.join('<p></p>',$faker->paragraphs(5)) .'</p>'; 

            $ad = new Ad();

            $user = $users[mt_rand(0,count($users) -1)];

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContain($contain)
                ->setPrice(mt_rand(40,200))
                ->setRooms(mt_rand(1,6))
                ->setAuthor($user);

            // Nous gérons les images d'une annonce
            for ($j=1;$j<=mt_rand(2,5);$j++){
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                      ->setCaption($faker->sentence())
                      ->setAd($ad);

                $manager->persist($image);
            }

            // Gestion des réservations
            for($j = 1; $j <= mt_rand(0, 10); $j++){
                $booking = new Booking();

                $createdAt = $faker->dateTimeBetween('-6 months');
                $startDate = $faker->dateTimeBetween('-3 months');

                // Gestion de la date de fin
                $duration = mt_rand(3,10);
                $endDate = (clone $startDate)->modify("+$duration days");

                $amount = $ad->getPrice() * $duration;

                $booker = $users[mt_rand(0, count($users) -1)];

                $comment = $faker->paragraph();

                $booking->setBooker($booker)
                        ->setAd($ad)
                        ->setStartDate($startDate)
                        ->setEndDate($endDate)
                        ->setCreatedAt($createdAt)
                        ->setAmount($amount)
                        ->setComment($comment);
                
                $manager->persist($booking);
            }

            $manager->persist($ad);
        }

        $manager->flush();
    }
}
