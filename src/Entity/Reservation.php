<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\DateTimeValidator;
use Psr\Log\LoggerInterface;

use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThanOrEqual("now Europe/Paris",message="la reservation doit etre superieur à aujourdhui")
     * 
     */
    private $DateHeureDebut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $DateHeureFin;

    /** 
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $TitreEvenements;
  

    /**
     * @ORM\ManyToOne(targetEntity=Salle::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $salle;

    /**
     * @ORM\ManyToOne(targetEntity=Formateur::class, inversedBy="formateur")
     * @ORM\JoinColumn(nullable=false)
     */
    private $formateur;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->DateHeureDebut;
    }

    public function setDateHeureDebut(\DateTimeInterface $DateHeureDebut): self
    {
        $this->DateHeureDebut = $DateHeureDebut;

        return $this;
    }

    public function getDateHeureFin(): ?\DateTimeInterface
    {
        return $this->DateHeureFin;
    }

    public function setDateHeureFin(?\DateTimeInterface $DateHeureFin): self
    {
        $this->DateHeureFin = $DateHeureFin;

        return $this;
    }

    public function getTitreEvenements(): ?string
    {
        return $this->TitreEvenements;
    }

    public function setTitreEvenements(string $TitreEvenements): self
    {
        $this->TitreEvenements = $TitreEvenements;

        return $this;
    }

   

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): self
    {
        $this->salle = $salle;

        return $this;
    }

    public function getFormateur(): ?Formateur
    {
        return $this->formateur;
    }

    public function setFormateur(?Formateur $formateur): self
    {
        $this->formateur = $formateur;

        return $this;
    }
    public function dateTimeRangesIntersect($aBegin, $aEnd, $bBegin, $bEnd)
    {
        

        return ($aBegin > $bBegin && $aEnd < $bEnd) // Le deuxième interval recouvre le premier
            || ($aBegin > $bBegin && $aBegin < $bEnd)  // Le deuxième interval recouvre le début du premier
            || ($aEnd > $bBegin && $aBegin < $bBegin) // Le deuxième interval recouvre la fin du premier
            || ($aBegin < $bBegin && $aEnd > $bEnd); // Le deuxième interval est inclu dans le premier
    }
    
}
