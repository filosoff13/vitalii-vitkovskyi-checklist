<?php

declare(strict_types=1);

namespace App\Service\Integration;

use App\Entity\ApiIntegration;

class IntegrationContext
{
    private DvCampusNotelistIntegrationStrategy $dvCampusNotelistIntegrationStrategy;

    /**
     * @param DvCampusNotelistIntegrationStrategy $dvCampusNotelistIntegrationStrategy
     */
    public function __construct(DvCampusNotelistIntegrationStrategy $dvCampusNotelistIntegrationStrategy)
    {
        $this->dvCampusNotelistIntegrationStrategy = $dvCampusNotelistIntegrationStrategy;
    }


    public function getStrategy(int $type): AbstractIntegrationStrategy {
        switch ($type) {
            case 0: return $this->dvCampusNotelistIntegrationStrategy;
            default: throw new \LogicException('Invalid type');
        }
    }

    public function create(int $type, array $data): ApiIntegration {
        $this->getStrategy($type)->create($data);
    }
}