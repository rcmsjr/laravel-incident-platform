<?php

namespace Modules\User\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Staff = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Staff => 'Staff',
        };
    }

    public function canManageUsers(): bool
    {
        return $this === self::Admin;
    }
}
