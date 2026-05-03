<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserCredential extends Authenticatable
{
    protected $table      = 'user_credentials';
    protected $primaryKey = 'credential_id';

    protected $fillable = [
        'employee_id',
        'username',
        'password_hash',
        'is_active',
    ];

    protected $hidden = ['password_hash'];

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    public function hasRole(string $role): bool
    {
        $access = $this->userAccess;

        if (! $access) {
            return false;
        }

        if ($role === 'admin' && isset($access->is_admin)) {
            return (bool) $access->is_admin;
        }

        if (isset($access->role)) {
            return $access->role === $role;
        }

        return false;
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function userAccess()
    {
        return $this->hasOne(UserAccess::class, 'employee_id', 'employee_id')
                    ->where('is_active', 1);
    }
}