<?php


namespace App\Domain\TimeEntry\Entity;

use App\Domain\User\Entity\User;
use App\Infrastructure\Persistence\Doctrine\TimeEntryRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity(repositoryClass: TimeEntryRepository::class)]
class TimeEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'date')]
    private \DateTimeInterface $date;

    #[ORM\Column(type: 'time')]
    private \DateTimeInterface $startTime;

    #[ORM\Column(type: 'time')]
    private \DateTimeInterface $endTime;

    #[ORM\Column(type: 'integer')]
    private int $duration;

    #[ORM\Column(type: 'string', length: 255)]
    private string $description;

    #[ORM\Column(type: 'string', length: 255)]
    private string $project;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public static function create(
        User $user,
        string $date,
        string $startTime,
        string $endTime,
        int $duration,
        string $description,
        ?string $project = null
    ): self {
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        $start = DateTime::createFromFormat('H:i', $startTime);
        $end = DateTime::createFromFormat('H:i', $endTime);

        if (!$dateObj || !$start || !$end) {
            throw new DomainException('Invalid date or time format.');
        }

        if ($start >= $end) {
            throw new DomainException('Start time must be before end time.');
        }

        if ($duration <= 0) {
            throw new DomainException('Duration must be a positive number.');
        }

        if (strlen(trim($description)) < 1) {
            throw new DomainException('Description must be at least 3 characters long.');
        }

        $entry = new self();
        $entry->user = $user;
        $entry->date = $dateObj;
        $entry->startTime = $start;
        $entry->endTime = $end;
        $entry->duration = $duration;
        $entry->description = $description;
        $entry->project = $project;

        return $entry;
    }

    public function update(
        string $date,
        string $startTime,
        string $endTime,
        int $duration,
        string $description,
        ?string $project = null
    ): void {
        $updated = self::create($this->user, $date, $startTime, $endTime, $duration, $description, $project);

        $this->date = $updated->date;
        $this->startTime = $updated->startTime;
        $this->endTime = $updated->endTime;
        $this->duration = $updated->duration;
        $this->description = $updated->description;
        $this->project = $updated->project;
    }

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
}
