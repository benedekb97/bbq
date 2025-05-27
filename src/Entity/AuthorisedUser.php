<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;

#[Entity]
class AuthorisedUser
{
    #[Column(type: Types::INTEGER), Id, GeneratedValue]
    public ?int $id = null;

    #[Column(type: Types::STRING)]
    public ?string $domain = null;

    #[Column(type: Types::STRING)]
    public ?string $userId = null;
}