<?php

declare(strict_types=1);

namespace App\Service\Integration;

use App\Entity\ApiIntegration;
use App\Entity\User;

class IntegrationContext
{
    private DvCampusNotelistIntegrationStrategy $dvCampusNotelistIntegrationStrategy;

    /** @var AbstractIntegrationStrategy[] */
    private array $strategies = [];

    /**
     * @param DvCampusNotelistIntegrationStrategy $dvCampusNotelistIntegrationStrategy
     */
    public function __construct(DvCampusNotelistIntegrationStrategy $dvCampusNotelistIntegrationStrategy)
    {
        $this->dvCampusNotelistIntegrationStrategy = $dvCampusNotelistIntegrationStrategy;
        $this->strategies[] = $dvCampusNotelistIntegrationStrategy;
    }

    public function getStrategy(int $type): AbstractIntegrationStrategy
    {
        switch ($type) {
            case 0: return $this->dvCampusNotelistIntegrationStrategy;
            default: throw new \LogicException('Invalid type');
        }
    }

    public function saveIntegrations(array $data, User $user): void
    {
        foreach ($this->strategies as $strategy) {
            $strategy->save($data, $user);
        }
    }

    public function create(int $type, array $data, User $user): ApiIntegration
    {
        $this->getStrategy($type)->create($data, $user);
        return new ApiIntegration();
    }
}
