<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Formateur;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
    $this->passwordEncoder = $passwordEncoder;
    }
    
  
    public function load(ObjectManager $manager)
    {
        $user = new Formateur();
        $user->setId('1');
        $user->setName('sad');
        $user->setSurname('elali');
        $user->setAdresse('5 Allée René lalique 54270 Essey les Nancy');
        $user->setMail('saddek1984@gmail.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->passwordEncoder->encodePassword($user,'saddik1984!'));
        $manager->persist($user);
        $manager->flush();
    }
    
}
