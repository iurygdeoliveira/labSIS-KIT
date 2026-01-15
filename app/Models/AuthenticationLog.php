<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use MongoDB\Laravel\Eloquent\Model;

/**
 * Model de Log de Autenticação armazenado no MongoDB.
 *
 * Este model sobrescreve o AuthenticationLog padrão do pacote
 * rappasoft/laravel-authentication-log para utilizar MongoDB
 * em vez de PostgreSQL, otimizando o armazenamento de dados
 * de auditoria que crescem rapidamente e não necessitam de
 * relacionamentos complexos.
 *
 * @property string $_id MongoDB ObjectId
 * @property string $authenticatable_type
 * @property int $authenticatable_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon|null $login_at
 * @property bool $login_successful
 * @property \Illuminate\Support\Carbon|null $logout_at
 * @property bool $cleared_by_user
 * @property array|null $location
 */
class AuthenticationLog extends Model
{
    /**
     * Define que este model usa a conexão MongoDB.
     */
    protected $connection = 'mongodb';

    /**
     * Nome da collection no MongoDB.
     */
    protected $collection = 'authentication_log';

    /**
     * Desabilita timestamps automáticos (compatível com o pacote original).
     */
    public $timestamps = false;

    /**
     * Campos que podem ser preenchidos em massa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'authenticatable_type',
        'authenticatable_id',
        'ip_address',
        'user_agent',
        'login_at',
        'login_successful',
        'logout_at',
        'cleared_by_user',
        'location',
    ];

    /**
     * Define os casts dos atributos.
     */
    protected function casts(): array
    {
        return [
            'cleared_by_user' => 'boolean',
            'location' => 'array',
            'login_successful' => 'boolean',
            'login_at' => 'datetime',
            'logout_at' => 'datetime',
        ];
    }

    /**
     * Relacionamento polimórfico com o usuário autenticável.
     */
    public function authenticatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope para buscar logs de autenticações bem-sucedidas.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('login_successful', true);
    }

    /**
     * Scope para buscar logs de autenticações falhadas.
     */
    public function scopeFailed($query)
    {
        return $query->where('login_successful', false);
    }

    /**
     * Scope para buscar logs recentes.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('login_at', '>=', now()->subDays($days));
    }

    /**
     * Scope para logs ainda ativos (sem logout).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('logout_at');
    }
}
