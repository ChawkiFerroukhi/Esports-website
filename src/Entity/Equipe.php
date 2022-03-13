<?php

namespace App\Entity;

use App\Repository\EquipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;

/**
  * @ORM\Entity(repositoryClass=EquipeRepository::class)
  * @UniqueEntity(
  * fields={"nom"},
  * errorPath="nom",
  * message="This name already exists try another name."
  *)
 */
class Equipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="equipe")
     */
    private $membres;

    /**
     * @ORM\ManyToMany(targetEntity=Jeux::class, inversedBy="equipes")
     */
    private $jeux;

    /**
     * @ORM\ManyToMany(targetEntity=Evenement::class, mappedBy="equipes")
     */
    private $evenements;

    /**
     * @ORM\OneToOne(targetEntity=Classement::class, mappedBy="equipe", cascade={"persist", "remove"})
     */
    private $classement;

    public function __construct()
    {
        $this->membres = new ArrayCollection();
        $this->jeux = new ArrayCollection();
        $this->evenements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    // public function __toString() {
    //     return (String) $this -> getNom();
    // }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|user[]
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(User $membre): self
    {
        if (!$this->membres->contains($membre)) {
            $this->membres[] = $membre;
            $membre->setEquipe($this);
        }

        return $this;
    }

    public function removeMembre(User $membre): self
    {
        if ($this->membres->removeElement($membre)) {
            // set the owning side to null (unless already changed)
            if ($membre->getEquipe() === $this) {
                $membre->setEquipe(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Jeux[]
     */
    public function getJeux(): Collection
    {
        return $this->jeux;
    }

    public function addJeux(Jeux $jeux): self
    {
        if (!$this->jeux->contains($jeux)) {
            $this->jeux[] = $jeux;
        }

        return $this;
    }

    public function removeJeux(Jeux $jeux): self
    {
        $this->jeux->removeElement($jeux);

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getEvenements(): Collection
    {
        return $this->evenements;
    }

    public function addEvenement(Evenement $evenement): self
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements[] = $evenement;
            $evenement->addEquipe($this);
        }

        return $this;
    }

    public function removeEvenement(Evenement $evenement): self
    {
        if ($this->evenements->removeElement($evenement)) {
            $evenement->removeEquipe($this);
        }

        return $this;
    }

    public function getClassement(): ?Classement
    {
        return $this->classement;
    }

    public function setClassement(?Classement $classement): self
    {
        // unset the owning side of the relation if necessary
        if ($classement === null && $this->classement !== null) {
            $this->classement->setEquipe(null);
        }

        // set the owning side of the relation if necessary
        if ($classement !== null && $classement->getEquipe() !== $this) {
            $classement->setEquipe($this);
        }

        $this->classement = $classement;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->getEvenements();
    }

}