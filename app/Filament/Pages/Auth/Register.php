<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use App\Events\TenantCreated;
use App\Events\UserRegistered;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\Filament\NotificationsTrait;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    use NotificationsTrait;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados de Acesso')
                    ->components([
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
                    ->columns(1),
            ]);
    }

    protected function handleRegistration(array $data): Model
    {
        try {
            // Separar dados do usuário e do tenant
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                // email_verified_at será null - usuário precisa verificar email
                // approved_at será null - usuário precisa ser aprovado pelo admin
            ];

            $tenantData = [
                'name' => $data['tenant_name'],
                'is_active' => true,
            ];

            // Criar usuário
            $user = User::create($userData);

            // Criar tenant
            $tenant = Tenant::create($tenantData);

            // Associar usuário ao tenant
            $user->tenants()->attach($tenant->id);

            // Disparar eventos (temporariamente desabilitado para teste)
            // event(new UserRegistered($user, $data['password']));
            // event(new TenantCreated($user, $tenant));

            // Notificação de sucesso
            $this->notifySuccess(
                'Cadastro realizado com sucesso!',
                'Sua conta foi criada e está aguardando aprovação. Você receberá um email quando for aprovado.'
            );

            return $user;

        } catch (QueryException $e) {
            // Verificar se é erro de email duplicado
            if ($e->getCode() === '23505' && str_contains($e->getMessage(), 'users_email_unique')) {
                $this->notifyDanger(
                    'Email já cadastrado',
                    'O email informado já está sendo usado por outro usuário. Por favor, use um email diferente.'
                );
            }
            // Verificar se é erro de nome de tenant duplicado
            elseif ($e->getCode() === '23505' && str_contains($e->getMessage(), 'tenants_name_unique')) {
                $this->notifyDanger(
                    'Nome do tenant já existe',
                    'O nome da organização informado já está sendo usado. Por favor, escolha um nome diferente.'
                );
            }
            // Outros erros de banco de dados
            else {
                $this->notifyDanger(
                    'Erro no cadastro',
                    'Ocorreu um erro inesperado durante o cadastro. Tente novamente em alguns instantes.'
                );
            }

            // Re-lançar a exceção para que o Filament trate adequadamente
            throw $e;
        } catch (\Exception $e) {
            // Capturar outras exceções não relacionadas ao banco
            $this->notifyDanger(
                'Erro no cadastro',
                'Ocorreu um erro inesperado durante o cadastro. Tente novamente em alguns instantes.',
                10,
                true
            );

            // Re-lançar a exceção
            throw $e;
        }
    }

    protected function afterRegister(): void
    {
        // Fazer logout do usuário para evitar login automático
        Filament::auth()->logout();

        // Redirecionar para login com notificação
        $this->redirect('/login');
    }

    protected function getRedirectUrl(): string
    {
        return '/login';
    }
}
