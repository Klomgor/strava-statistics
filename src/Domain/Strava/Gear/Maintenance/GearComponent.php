<?php

declare(strict_types=1);

namespace App\Domain\Strava\Gear\Maintenance;

use App\Domain\Strava\Gear\GearIds;
use App\Infrastructure\ValueObject\String\Name;

final readonly class GearComponent
{
    private MaintenanceTasks $maintenanceTasks;

    private function __construct(
        private Tag $tag,
        private Name $label,
        private GearIds $attachedTo,
        private ?string $imgSrc,
    ) {
        $this->maintenanceTasks = MaintenanceTasks::empty();
    }

    public static function create(
        Tag $tag,
        Name $label,
        GearIds $attachedTo,
        ?string $imgSrc,
    ): self {
        return new self(
            tag: $tag,
            label: $label,
            attachedTo: $attachedTo,
            imgSrc: $imgSrc,
        );
    }

    public function addMaintenanceTask(MaintenanceTask $task): void
    {
        $this->maintenanceTasks->add($task);
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }

    public function getLabel(): Name
    {
        return $this->label;
    }

    public function getAttachedTo(): GearIds
    {
        return $this->attachedTo;
    }

    public function getImgSrc(): ?string
    {
        return $this->imgSrc;
    }

    public function getMaintenanceTasks(): MaintenanceTasks
    {
        return $this->maintenanceTasks;
    }
}
