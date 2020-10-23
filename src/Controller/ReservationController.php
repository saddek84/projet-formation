<?php

namespace App\Controller;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\SalleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    /**
     * @Route("/index", name="reservation_index", methods={"GET"})
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        
        
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
    /**
     * @Route("/calendar", name="reservation_calendar", methods={"GET"})
     * 
     */
    public function calendar(): Response   //renvoi le calendrier
    {
        
        
        
        return $this->render('reservation/calendar.html.twig');
                    
        
    }
   

    private function dateTimeRangesIntersect($aBegin, $aEnd, $bBegin, $bEnd)
    {
        
        return ($aBegin > $bBegin && $aEnd < $bEnd) // Le deuxième interval recouvre le premier
            || ($aBegin > $bBegin && $aBegin < $bEnd)  // Le deuxième interval recouvre le début du premier
            || ($aBegin < $bBegin && $aEnd > $bBegin) // Le deuxième interval recouvre la fin du premier
            || ($aBegin < $bBegin && $aEnd > $bEnd); // Le deuxième interval est inclu dans le premier
    }

    /**
     * @Route("/new", name="reservation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response   //creation d'une reservation
    {
        $reservation = new Reservation();
        $message='';
        $reservation->setDateHeureDebut(new \DateTime('now', new \DateTimeZone('Europe/Paris')));
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
        
        
        
        //envoi du formulaire si valide 
        if ($form->isSubmitted() && $form->isValid()) {
           

            $reservationRepository = $this->getDoctrine()->getRepository(Reservation::class); //appel requete preparée 
            $duration = $form['duration']->getData();
            $salle = $form['salle']->getData();
            $reservation->setDateHeureFin(clone $reservation->getDateHeureDebut());
            $reservation->getDateHeureFin()->add($duration);
            $entityManager = $this->getDoctrine()->getManager();   // connexion à la base de donnees           
            $reservations = $reservationRepository->findBySalle($salle->getlibellesalle()); //recuperation des données en fonction de la salle
            dump($reservation->getDateHeureFin()->format('H'));
            

            if(!empty($reservations)) {

                foreach($reservations as $existingReservation){
                    
                    //teste des dates enregistre dans la base avec nouvelle date de reservation
                    if ($this->dateTimeRangesIntersect(
                            $existingReservation->getDateHeureDebut(), 
                            $existingReservation->getDateHeureFin(),
                            $reservation->getDateHeureDebut(), 
                            $reservation->getDateHeureFin()
                    )
                    ) {
                        dump($existingReservation);
                        $message="plage horaire non valide";
                        return $this->render('reservation/new.html.twig', [
                            'reservation' => $reservation,
                            'message'=>$message,
                            'form' => $form->createView(),
                        ]);
                    
                    }
                    else if($reservation->getDateHeureFin()->format('H')< 18)
                    {
                        dump($existingReservation);
                        $entityManager->persist($reservation);
                        $entityManager->flush();
                        return $this->redirectToRoute('reservation_calendar');    
                       
                      
                    }
                    else{
                        $message = "les reservations ne peuvent exceder 18h";
                        return $this->render('reservation/new.html.twig', [
                            'reservation' => $reservation,
                            'message'=>$message,
                            'form' => $form->createView(),
                        ]);
                    }  
                }      
                
                       
                
            }
            else {
                
                dump($salle);
                $entityManager->persist($reservation);
                $entityManager->flush();
                return $this->redirectToRoute('reservation_calendar');        
            }
          
        }
        
        
      
        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'message'=>$message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_show", methods={"GET"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reservation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservation_index');
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Reservation $reservation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reservation_index');
    }
    
    

      

}
 
