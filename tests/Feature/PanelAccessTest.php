<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function (): void {
    /** @var \Tests\TestCase $this */
    $this->seed(DatabaseSeeder::class);
});

describe('Acesso aos Painéis', function (): void {
    it('redireciona visitantes para o login ao tentar acessar painéis protegidos', function (): void {
        /** @var \Tests\TestCase $this */
        $this->get('/admin')->assertRedirect('/__compat-login');
        $this->get('/user')->assertRedirect('/__compat-login');
    });

    it('usuário admin pode acessar o painel administrativo', function (): void {
        /** @var \Tests\TestCase $this */
        $admin = User::where('email', 'admin@labsis.dev.br')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertSuccessful();
    });

    it('usuário admin é redirecionado para /admin ao acessar a página de login', function (): void {
        /** @var \Tests\TestCase $this */
        $admin = User::where('email', 'admin@labsis.dev.br')->firstOrFail();

        $this->actingAs($admin)
            ->get('/login')
            ->assertRedirect('/admin');
    });

    it('usuário comum não pode acessar o painel administrativo', function (): void {
        /** @var \Tests\TestCase $this */
        $user = User::where('email', 'beltrano@labsis.dev.br')->firstOrFail();

        // O comportamento padrão do Filament/Policies para acesso não autorizado costuma ser 403
        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    });

    it('usuário comum com tenant pode acessar o painel do usuário diretamente', function (): void {
        /** @var \Tests\TestCase $this */
        // Beltrano é User no Tenant A e Owner no Tenant B. Vamos testar acesso ao Tenant A.
        $user = User::where('email', 'beltrano@labsis.dev.br')->firstOrFail();
        $tenant = Tenant::where('name', 'Tenant A')->firstOrFail();

        $this->actingAs($user)
            ->get("/user/{$tenant->uuid}")
            ->assertSuccessful();
    });

    it('usuário comum é redirecionado para o painel do tenant ao acessar a página de login', function (): void {
        /** @var \Tests\TestCase $this */
        // Sicrano é Owner no Tenant A e User no Tenant B
        $user = User::where('email', 'sicrano@labsis.dev.br')->firstOrFail();

        // A lógica do middleware pega o primeiro tenant do usuário
        $firstTenant = $user->getTenants(\Filament\Facades\Filament::getPanel('user'))->first();

        $this->actingAs($user)
            ->get('/login')
            ->assertRedirect("/user/{$firstTenant->uuid}");
    });
});
