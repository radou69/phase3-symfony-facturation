<?php

namespace App\Entity;

use App\Repository\InvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $number = null;

    #[ORM\Column(length: 50)]
    private string $status = 'draft'; // draft | pending_payment | paid

    #[ORM\Column]
    private float $total_ttc = 0;

    #[ORM\Column]
    private ?\DateTime $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\OneToMany(targetEntity: InvoiceItem::class, mappedBy: 'invoice', orphanRemoval: true)]
    private Collection $invoiceItems;

    public function __construct()
    {
        $this->invoiceItems = new ArrayCollection();
        $this->created_at   = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getNumber(): ?string { return $this->number; }
    public function setNumber(string $number): static { $this->number = $number; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getTotalTtc(): float { return $this->total_ttc; }
    public function setTotalTtc(float $total_ttc): static { $this->total_ttc = $total_ttc; return $this; }

    public function getCreatedAt(): ?\DateTime { return $this->created_at; }
    public function setCreatedAt(\DateTime $created_at): static { $this->created_at = $created_at; return $this; }

    public function getOwner(): ?User { return $this->owner; }
    public function setOwner(?User $owner): static { $this->owner = $owner; return $this; }

    public function getClient(): ?Client { return $this->client; }
    public function setClient(?Client $client): static { $this->client = $client; return $this; }

    // --- InvoiceItems ---
    public function getInvoiceItems(): Collection { return $this->invoiceItems; }

    public function addInvoiceItem(InvoiceItem $item): static
    {
        if (!$this->invoiceItems->contains($item)) {
            $this->invoiceItems->add($item);
            $item->setInvoice($this);
        }
        return $this;
    }

    public function removeInvoiceItem(InvoiceItem $item): static
    {
        if ($this->invoiceItems->removeElement($item)) {
            if ($item->getInvoice() === $this) {
                $item->setInvoice(null);
            }
        }
        return $this;
    }

    // Recalcule le total à partir des lignes
    public function computeTotal(): void
    {
        $total = 0;
        foreach ($this->invoiceItems as $item) {
            $total += $item->getQuantity() * (float) $item->getProduct()->getPrice();
        }
        $this->total_ttc = $total;
    }
}