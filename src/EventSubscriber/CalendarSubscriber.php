<?php

namespace App\EventSubscriber;

use App\Repository\ReservationRepository;
use App\Repository\CalendrierRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $ReservationRepository;
    private $CalendrierRepository;
    private $router;
    

    public function __construct(
        ReservationRepository $ReservationRepository,
        CalendrierRepository $CalendrierRepository,
        UrlGeneratorInterface $router
        
    ) {
        $this->ReservationRepository = $ReservationRepository;
        $this->CalendrierRepository = $CalendrierRepository;
        $this->router = $router;
        
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        //$jourferies=$this->fetes($calendar->getStart()->format('Y'));
        $start = $calendar->getStart();
        $end = $calendar->getEnd();        
        $filters = $calendar->getFilters();
        //dump($jourferies);
        
        $reservations = $this->ReservationRepository
            ->createQueryBuilder('reservation')                
            ->where('reservation.DateHeureDebut BETWEEN :start and :end OR reservation.DateHeureFin BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->setParameter('end', $end->format('Y-m-d H:i:s'))
            ->getQuery()
            ->getResult()
        ;
        
        $jourFetes = $this->CalendrierRepository
        ->createQueryBuilder('jours')    
            ->where('jours.date BETWEEN :start and :end')
            ->setParameter('start', $start->format('Y-m-d '))
            ->setParameter('end', $end->format('Y-m-d '))
           ->getQuery()
            ->getResult()
        ;
      
       foreach($jourFetes as $jours){
       $joursFerieEvent=new Event(
          $jours->getNomJourFerie(),
          $jours->getDate(),
          $jours->getDate(),

            

            
        );
        dump($joursFerieEvent);
        $joursFerieEvent->setAllDay(true);
        $joursFerieEvent->setOptions([

          
          'display' => 'auto',
          'backgroundColor' => 'dark',
          'borderColor' => 'red',
        ]);
        
        $calendar->addEvent($joursFerieEvent);
        }
        foreach ($reservations as $reservation) {
          
            // this create the events with your data (here booking data) to fill calendar
                $reservationEvent = new Event(
                $reservation->getSalle()->getlibelleSalle() . " - " . $reservation->getTitreEvenements(),
                $reservation->getDateHeureDebut(),
                $reservation->getDateHeureFin(), // If the end date is null or not defined, a all day event is created.
                $options = ['id'=> $reservation->getSalle()->getId()],
            );
           
            
            /*
             * Add custom options to events
             *
             * For more information see: https://fullcalendar.io/docs/event-object
             * and: https://github.com/fullcalendar/fullcalendar/blob/master/src/core/options.ts
             */
          dump($reservationEvent->toArray()); 
          dump(in_array('1' ,$reservationEvent->toArray()));
          dump($reservationEvent->getOptions());
          if(in_array('1',$reservationEvent->toArray())){

              $reservationEvent->setOptions([
                
                'backgroundColor' => 'green',
                'borderColor' => 'green',
                
              ]);
          }
          else{      
          $reservationEvent->setOptions([
                
            'backgroundColor' => 'red',
            'borderColor' => 'red',
            
          ]);   
          } 
            $reservationEvent->addOption(
                'url',
                $this->router->generate('reservation_show', [
                    'id' => $reservation->getId(),
                ])
            );

            // finally, add the event to the CalendarEvent to fill the calendar
            $reservationEvent->addOption(
            
              'resources',  $reservation->getSalle()->getLibelleSalle()
            );
            dump($reservationEvent);
            $calendar->addEvent($reservationEvent);
        }
    }

      
}