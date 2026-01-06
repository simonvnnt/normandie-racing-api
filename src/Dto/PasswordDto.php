<?php

namespace App\Dto;

class PasswordDto
{
    public function __construct(
        public string $password
    )
    {}
}
