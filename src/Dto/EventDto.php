<?php

namespace App\Dto;

class EventDto {
    public function __construct(
        public ?string $name = null,
        public ?string $fromDate = null,
        public ?string $toDate = null,
    )
    {}
}
