<?php


namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TimeEntryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: TimeEntryRepository::class)]
#[ApiResource(
    routePrefix: '/api',
    normalizationContext: ['groups' => ['time_entry:read']],
    denormalizationContext: ['groups' => ['time_entry:write']],
    security: "is_granted('IS_AUTHENTICATED_FULLY')"
)]
class TimeEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['time_entry:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    #[Groups(['time_entry:write'])]
    #[Context(normalizationContext: ['datetime_format' => 'Y-m-d'])]
    private \DateTimeInterface $date;

    #[ORM\Column(type: 'time')]
    #[Groups(['time_entry:write'])]
    private \DateTimeInterface $startTime;

    #[ORM\Column(type: 'time')]
    #[Groups(['time_entry:write'])]
    private \DateTimeInterface $endTime;

    #[ORM\Column(type: 'integer')]
    #[Groups(['time_entry:read', 'time_entry:write'])]
    private int $duration;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['time_entry:read', 'time_entry:write'])]
    private string $description;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['time_entry:read', 'time_entry:write'])]
    private string $project;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['time_entry:write'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getStartTime(): \DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): \DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getProject(): string
    {
        return $this->project;
    }

    public function setProject(string $project): void
    {
        $this->project = $project;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    #[Groups(['time_entry:read'])]
    #[SerializedName('date')]
    public function getDateFormatted(): string
    {
        return $this->date->format('Y-m-d');
    }

    #[Groups(['time_entry:read'])]
    #[SerializedName('startTime')]
    public function getStartTimeFormatted(): string
    {
        return $this->startTime->format('H:i');
    }

    #[Groups(['time_entry:read'])]
    #[SerializedName('endTime')]
    public function getEndTimeFormatted(): string
    {
        return $this->endTime->format('H:i');
    }

    #[Groups(['time_entry:read'])]
    #[SerializedName('userId')]
    public function getUserId(): int
    {
        return $this->user->getId();
    }
}
