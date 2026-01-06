<?php

namespace App\Dto;

class RegisterDto
{
    public function __construct(
        public string $username,
        public string $password
    )
    {}
}
