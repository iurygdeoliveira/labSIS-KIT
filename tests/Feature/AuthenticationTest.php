<?php

declare(strict_types=1);

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

describe('Fluxo de Login', function () {
    it('página de login está renderizando', function () {
        /** @var \Tests\TestCase $this */
        // Verifica se a página de login carrega com sucesso
        $this->get('/login')->assertSuccessful();
    });

    it('usuário aprovado pode fazer login com credenciais corretas', function () {
        /** @var \Tests\TestCase $this */
        $password = 'password';
        // Garantir que o usuário esteja aprovado e não suspenso
        $user = User::factory()->create([
            'password' => Hash::make($password),
            'is_approved' => true,
            'is_suspended' => false,
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $user->email,
                'password' => $password,
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors() // Verifica se não houve erros de validação no formulário
            ->assertRedirect('/'); // Verifica se o redirecionamento para a home ocorreu

        // Verifica se o usuário está autenticado
        $this->assertAuthenticatedAs($user);
    });

    it('usuário não pode fazer login com credenciais incorretas', function () {
        /** @var \Tests\TestCase $this */
        $user = User::factory()->create([
            'password' => Hash::make('password'),
            'is_approved' => true,
            'is_suspended' => false,
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => $user->email,
                'password' => 'wrong-password',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email']); // Verifica se houve erro de validação no campo email

        // Verifica se o usuário continua como visitante (não logado)
        $this->assertGuest();
    });
});

describe('Fluxo de Registro', function () {
    it('página de registro está renderizando', function () {
        /** @var \Tests\TestCase $this */
        // Verifica se a página de registro carrega com sucesso
        $this->get('/register')->assertSuccessful();
    });

    it('pode criar uma nova conta com tenant', function () {
        /** @var \Tests\TestCase $this */
        $newData = User::factory()->make();
        $tenantName = 'Novo Tenant Teste';

        Livewire::test(Register::class)
            ->fillForm([
                'name' => $newData->name,
                'email' => $newData->email,
                'password' => 'password',
                'password_confirmation' => 'password',
                'tenant_name' => $tenantName,
            ])
            ->call('register')
            ->assertHasNoFormErrors() // Verifica se não houve erros de validação no formulário
            ->assertRedirect(); // Verifica se houve redirecionamento após o registro

        // Verifica se o usuário foi autenticado automaticamente
        $this->assertAuthenticated();

        // Verifica se o usuário foi criado no banco de dados com os status corretos
        $this->assertDatabaseHas('users', [
            'email' => $newData->email,
            'is_suspended' => true, // Conforme lógica do Register.php
            'is_approved' => false,
        ]);

        // Verifica se o tenant foi criado no banco de dados
        $this->assertDatabaseHas('tenants', [
            'name' => $tenantName,
        ]);
    });
});

describe('Fluxo de Recuperação de Senha', function () {
    it('pode renderizar a página de recuperação de senha', function () {
        /** @var \Tests\TestCase $this */
        // Verifica se a página de recuperação de senha carrega com sucesso
        $this->get('/password-reset/request')->assertSuccessful();
    });

    it('pode solicitar link de redefinição de senha', function () {
        $user = User::factory()->create();

        Livewire::test(RequestPasswordReset::class)
            ->fillForm([
                'email' => $user->email,
            ])
            ->call('request')
            ->assertHasNoFormErrors(); // Verifica se a solicitação foi enviada sem erros
    });
});
