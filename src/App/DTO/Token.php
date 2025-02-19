<?php

declare(strict_types=1);

namespace App\DTO;

class Token
{
    /**
     * @param string $id
     * @param string[] $permissions
     */
    public function __construct(
        private readonly string $id,
        private readonly array $permissions
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function hasPermission(string $permission): bool
    {
        return in_array($permission, $this->permissions, true);
    }
}
