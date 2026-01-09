<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Models\Tenant;
use App\Models\User;
use App\Traits\Filament\NotificationsTrait;
use Exception;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    use NotificationsTrait;

    #[\Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name')
                    ->label('Nome completo')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(User::class),
                TextInput::make('password')
                    ->label('Senha')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->confirmed()
                    ->revealable()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),
                TextInput::make('password_confirmation')
                    ->label('Confirmar senha')
                    ->password()
                    ->required()
                    ->revealable()
                    ->dehydrated(false),
                TextInput::make('tenant_name')
                    ->label('Nome do Tenant')
                    ->required()
                    ->maxLength(255)
                    ->unique(Tenant::class, 'name'),
            ])
            ->columns(1);
    }

    /**
     * Sobrescreve o método para não enviar verificação de email
     * já que desabilitamos a verificação de email no AuthPanelProvider
     */
    #[\Override]
    protected function sendEmailVerificationNotification($user): void
    {
        // Não faz nada - verificação de email desabilitada
    }

    #[\Override]
    protected function handleRegistration(array $data): Model
    {
        try {
            $userData = $this->prepareUserData($data);
            $tenantData = $this->prepareTenantData($data);

            $user = $this->createUser($userData);
            $tenant = $this->createTenant($tenantData);

            $this->associateUserWithTenant($user, $tenant);

            // Disparar evento de usuário registrado
            event(new \App\Events\UserRegistered($user));

            $this->showSuccessNotification();

            return $user;

        } catch (QueryException $e) {
            $this->handleDatabaseException($e);
            throw $e;
        } catch (Exception $e) {
            $this->handleGenericException($e);
            throw $e;
        }
    }

    /**
     * Prepara os dados do usuário para criação
     */
    protected function prepareUserData(array $data): array
    {
        return [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'is_suspended' => true, // Usuário fica suspenso até aprovação
            'is_approved' => false, // Usuário não aprovado por padrão
            'email_verified_at' => null, // Email não verificado por padrão
        ];
    }

    /**
     * Prepara os dados do tenant para criação
     */
    protected function prepareTenantData(array $data): array
    {
        return [
            'name' => $data['tenant_name'],
            'is_active' => true,
        ];
    }

    /**
     * Cria o usuário no banco de dados
     */
    protected function createUser(array $userData): User
    {
        return User::create($userData);
    }

    /**
     * Cria o tenant no banco de dados
     */
    protected function createTenant(array $tenantData): Tenant
    {
        return Tenant::create($tenantData);
    }

    /**
     * Associa o usuário ao tenant e atribui role Owner
     */
    protected function associateUserWithTenant(User $user, Tenant $tenant): void
    {
        $user->tenants()->attach($tenant->id);

        // Atribuir role Owner para o usuário no tenant criado
        $this->assignOwnerRoleToUser($user, $tenant);
    }

    /**
     * Atribui a role Owner para o usuário no tenant
     */
    protected function assignOwnerRoleToUser(User $user, Tenant $tenant): void
    {
        // Buscar ou criar a role Owner para o tenant
        $ownerRole = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'Owner',
            'team_id' => $tenant->id,
        ], [
            'guard_name' => 'web',
        ]);

        // Atribuir a role ao usuário
        $user->rolesWithTeams()->syncWithoutDetaching([
            $ownerRole->id => ['team_id' => $tenant->id],
        ]);
    }

    /**
     * Exibe a notificação de sucesso
     */
    protected function showSuccessNotification(): void
    {
        $this->notifySuccess(
            'Cadastro realizado com sucesso!',
            'Sua conta foi criada e está suspensa até aprovação. Você receberá um email quando for aprovado.'
        );
    }

    /**
     * Trata exceções específicas do banco de dados
     */
    protected function handleDatabaseException(QueryException $e): void
    {
        if ($e->getCode() === '23505' && str_contains($e->getMessage(), 'users_email_unique')) {
            $this->notifyDanger(
                'Email já cadastrado',
                'O email informado já está sendo usado por outro usuário. Por favor, use um email diferente.'
            );
        } elseif ($e->getCode() === '23505' && str_contains($e->getMessage(), 'tenants_name_unique')) {
            $this->notifyDanger(
                'Nome do tenant já existe',
                'O nome da organização informado já está sendo usado. Por favor, escolha um nome diferente.'
            );
        } else {
            $this->notifyDanger(
                'Erro no cadastro',
                'Ocorreu um erro inesperado durante o cadastro. Tente novamente em alguns instantes.'
            );
        }
    }

    /**
     * Trata exceções genéricas
     */
    protected function handleGenericException(\Throwable $e): void
    {
        $this->notifyDanger(
            'Erro no cadastro',
            'Ocorreu um erro inesperado durante o cadastro. Tente novamente em alguns instantes.',
            10,
            true
        );
    }
}
