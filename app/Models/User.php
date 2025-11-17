<?php

namespace App\Models;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Campos asignables en masa.
     */
    public const ROLE_MASTER   = 'master';
    public const ROLE_OPERATOR = 'operator';

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'admin_role',
    ];

    /**
     * Campos ocultos en arrays/JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts de atributos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_admin'          => 'boolean',
            'admin_role'        => 'string',
        ];
    }

    /**
     * Relaciones
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isMasterAdmin(): bool
    {
        return $this->is_admin && $this->admin_role === self::ROLE_MASTER;
    }

    public function isOperatorAdmin(): bool
    {
        return $this->is_admin && $this->admin_role === self::ROLE_OPERATOR;
    }
}
