<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="user_id")
     */
    private $games;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mail;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Success", mappedBy="users")
     */
    private $successes;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->successes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setUserId($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getUserId() === $this) {
                $game->setUserId(null);
            }
        }

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * @return Collection|Success[]
     */
    public function getSuccesses(): Collection
    {
        return $this->successes;
    }

    public function addSuccess(Success $success): self
    {
        if (!$this->successes->contains($success)) {
            $this->successes[] = $success;
            $success->addUser($this);
        }

        return $this;
    }

    public function removeSuccess(Success $success): self
    {
        if ($this->successes->contains($success)) {
            $this->successes->removeElement($success);
            $success->removeUser($this);
        }

        return $this;
    }
}
