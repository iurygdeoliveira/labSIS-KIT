<?php

use Livewire\Livewire;

it('can view public home page', function (): void {
    $this->get('/')
        ->assertOk()
        ->assertSee('LabSIS');
});

it('can render home page livewire component', function (): void {
    Livewire::test('pages::home')
        ->assertOk()
        ->assertSee('LabSIS');
});
