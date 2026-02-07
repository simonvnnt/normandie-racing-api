<?php

namespace App\Dto;

class EventDto {
    public function __construct(
        public ?string $name = null,
        public ?string $link = null,
        public ?\DateTime $fromDate = null,
        public ?\DateTime $toDate = null,
    )
    {}
}
