<?php

use PHPUnit\Framework\TestCase;
use App\Entity\Reservation;
use Symfony\Component\Validator\Constraints\DateTime;

class ReservationTest extends TestCase
{
    public function testTruedateTimeRangesIntersect()  //teste unitaire de chevauchement des dates renvoi vrai
    {
        
        $reservation = new Reservation();
        $datedebut = new  \DateTime('2020-10-05 15:00:00');
        $datefin = new \DateTime('2020-10-05 16:00:00');
        $datedebut2 = new  \DateTime('2020-10-05 15:30:00');
        $datefin2 = new \DateTime('2020-10-05 17:00:00');
        $a = $datedebut;
        $b = $datefin;
        $c = $datedebut2;
        $d = $datefin2;

        $this->assertSame(true,$reservation->dateTimeRangesIntersect($a,$b,$c,$d));
    }
    public function testFalsedateTimeRangesIntersect() //teste unitaire de chevauchement des dates renvoi false
    {
        
        $reservation = new Reservation();
        $datedebut = new  \DateTime('2020-10-05 15:00:00');
        $datefin = new \DateTime('2020-10-05 16:00:00');
        $datedebut2 = new  \DateTime('2020-10-05 16:30:00');
        $datefin2 = new \DateTime('2020-10-05 17:00:00');
        $a = $datedebut;
        $b = $datefin;
        $c = $datedebut2;
        $d = $datefin2;

        $this->assertSame(false,$reservation->dateTimeRangesIntersect($a,$b,$c,$d));
    }
}
