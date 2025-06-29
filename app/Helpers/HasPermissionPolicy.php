<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasPermissionPolicy
{
    protected static function getPermissionAction(string $action): string
    {
        $resource = static::getModelLabel();
        $resource = Str::slug(Str::plural(Str::lower(class_basename(static::getModel()))), ' ');
        return "$action $resource";
    }
    public static function canAccess(): bool
    {
        return auth('web')->user()?->can(self::getPermissionAction('view'));
    }

    public static function canCreate(): bool
    {

        return auth('web')->user()?->can(self::getPermissionAction('create'));
    }

    public static function canEdit(Model $record): bool
    {
        return auth('web')->user()?->can(self::getPermissionAction('edit'));
    }

    public static function canDelete(Model $record): bool
    {
        return auth('web')->user()?->can(self::getPermissionAction('delete'));
    }
}
