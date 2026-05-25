<?php

declare(strict_types=1);

use App\Models\Team;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Filament\Facades\Filament;
use Tests\TestCase;

beforeEach(function (): void {
    /** @var TestCase $this */
    $this->seed(DatabaseSeeder::class);
});

describe('Acesso aos Painéis', function (): void {
    it('redireciona visitantes para o login ao tentar acessar painéis protegidos', function (): void {
        /** @var TestCase $this */
        $this->get('/admin')->assertRedirect('/__compat-login');
        $this->get('/user')->assertRedirect('/__compat-login');
    });

    it('usuário admin pode acessar o painel administrativo', function (): void {
        /** @var TestCase $this */
        $admin = User::where('email', 'admin@labsis.dev.br')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertSuccessful();
    });

    it('usuário admin pode acessar a listagem de usuários no Filament', function (): void {
        /** @var TestCase $this */
        $admin = User::where('email', 'admin@labsis.dev.br')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertSuccessful();
    });

    it('usuário admin é redirecionado para /admin ao acessar a página de login', function (): void {
        /** @var TestCase $this */
        $admin = User::where('email', 'admin@labsis.dev.br')->firstOrFail();

        $this->actingAs($admin)
            ->get('/login')
            ->assertRedirect('/admin');
    });

    it('usuário comum não pode acessar o painel administrativo', function (): void {
        /** @var TestCase $this */
        $user = User::where('email', 'beltrano@labsis.dev.br')->firstOrFail();

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    });

    it('usuário comum com team pode acessar o painel do usuário diretamente', function (): void {
        /** @var TestCase $this */
        $user = User::where('email', 'beltrano@labsis.dev.br')->firstOrFail();
        $team = Team::where('name', 'Team A')->firstOrFail();

        $this->actingAs($user)
            ->get("/user/{$team->slug}")
            ->assertSuccessful();
    });

    it('usuário comum é redirecionado para o painel do team ao acessar a página de login', function (): void {
        /** @var TestCase $this */
        $user = User::where('email', 'sicrano@labsis.dev.br')->firstOrFail();

        $firstTeam = $user->getTenants(Filament::getPanel('user'))->first();

        $this->actingAs($user)
            ->get('/login')
            ->assertRedirect("/user/{$firstTeam->slug}");
    });
});
