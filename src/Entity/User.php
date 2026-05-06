<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(length: 255)]
    private ?string $company_name = null;

    #[ORM\Column(length: 255)]
    private ?string $iban = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $siret = null;

    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $invoices;

    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'owner', orphanRemoval: true)]
    private Collection $clients;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->clients  = new ArrayCollection();
    }

    // --- Getters/Setters existants (inchangés) ---

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);
        return $data;
    }

    public function getFirstName(): ?string { return $this->first_name; }
    public function setFirstName(string $v): static { $this->first_name = $v; return $this; }

    public function getLastName(): ?string { return $this->last_name; }
    public function setLastName(string $v): static { $this->last_name = $v; return $this; }

    public function getCompanyName(): ?string { return $this->company_name; }
    public function setCompanyName(string $v): static { $this->company_name = $v; return $this; }

    public function getIban(): ?string { return $this->iban; }
    public function setIban(string $v): static { $this->iban = $v; return $this; }

    public function getSiret(): ?string { return $this->siret; }
    public function setSiret(?string $v): static { $this->siret = $v; return $this; }

    // --- Invoices ---
    public function getInvoices(): Collection { return $this->invoices; }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setOwner($this);
        }
        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            if ($invoice->getOwner() === $this) {
                $invoice->setOwner(null);
            }
        }
        return $this;
    }

    // --- Clients ---
    public function getClients(): Collection { return $this->clients; }

    public function addClient(Client $client): static
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setOwner($this);
        }
        return $this;
    }

    public function removeClient(Client $client): static
    {
        if ($this->clients->removeElement($client)) {
            if ($client->getOwner() === $this) {
                $client->setOwner(null);
            }
        }
        return $this;
    }
}