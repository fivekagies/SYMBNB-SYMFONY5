<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// HasLifecycleCallbacks que l'entite doit gerer son cycle de vie c-à-d à differents evenements
// de son cycle de vie on va relier
/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 * @ORM\HasLifecycleCallbacks()
 */

class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $booker;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ad", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ad;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface", message="Attention, la date d'arrivée doit etre au bon format !")
     * @Assert\GreaterThan("today", message="La date d'arrivée doit être ultérieur à la date d'aujourd'hui !",
     *     groups={"front"})
     *
     */
    private $startDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTimeInterface", message="Attention, la date de depart doit être au bon format !")
     * @Assert\GreaterThan(propertyPath="startDate", message="La date de départ doit etre plus éloignée que la date d'arrivée !")
     */
    private $endDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * Callback appelé à chaque fois qu'on crée une reservation
     *
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     *
     */
    public function prePersist(){
        if(empty($this->createdAt)){
            $this->createdAt = new \DateTime();
        }

        if(empty($this->amount)){
            //prix de l'annonce * nobre de jour
            $this->amount = $this->ad->getPrice() * $this->getDuration();

        }
    }

    public function getDuration(){
        $diff = $this->endDate->diff($this->startDate); // diif methode de classe DateTime renvoit objet DateInterval
        return $diff->days;
    }

    public function isBookableDates(){

        //1 il faut connaitre les dates qui sont impossibles pour l'annonce
        $notAvailableDays = $this->ad->getNotAvailableDays();
        //2 Il faut comparer les dates choisies avec les dates impossibles
        //faut il connaisse les jours de ma reservation le meme principe que getNotAvailableDays
        $bookingDays = $this->getDays();

        $formatDay = function ($day){
            return $day->format('Y-m-d');
        };
        //pour comparer il vaut mieux comparer des date en format string (plus facile à comparer) que comparer des DateTime

        // tableau des chaines de caraccteres de mes journées
        $days = array_map($formatDay,$bookingDays);

        $notAvailable = array_map($formatDay,$notAvailableDays);

        foreach ($days as $day){
            if(array_search($day,$notAvailable) !== false) return false;
        }

        return true;

    }

    /**
     * Permet de récupérer un tableau des journées qui correspondent à ma réservation
     *
     * @return array Un tableau d"objets DateTime représentant les jours de la reservation
     */
    public function getDays(){
        $resultat = range($this->startDate->getTimestamp(),
            $this->endDate->getTimestamp(),
            24*60*60);
        $days = array_map(function ($daysTimestamp){
            return new \DateTime(date('Y-m-d',$daysTimestamp));
        },$resultat);

        return $days;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBooker(): ?User
    {
        return $this->booker;
    }

    public function setBooker(?User $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAd(): ?Ad
    {
        return $this->ad;
    }

    public function setAd(?Ad $ad): self
    {
        $this->ad = $ad;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }


}
