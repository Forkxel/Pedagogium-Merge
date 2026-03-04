<?php
namespace App\Entity;

use App\Repository\UtmVisitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UtmVisitRepository::class)]
class UtmVisit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $utm_source;

    #[ORM\Column(length: 255)]
    private string $utm_medium;

    #[ORM\Column(length: 255)]
    private string $utm_campaign;

    #[ORM\Column]
    private \DateTimeImmutable $created_at;

    public function __construct(string $source, string $medium, string $campaign)
    {
        $this->utm_source = $source;
        $this->utm_medium = $medium;
        $this->utm_campaign = $campaign;
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtmSource(): string
    {
        return $this->utm_source;
    }

    public function getUtmMedium(): string
    {
        return $this->utm_medium;
    }

    public function getUtmCampaign(): string
    {
        return $this->utm_campaign;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->created_at;
    }
}
