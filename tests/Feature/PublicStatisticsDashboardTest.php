<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the public statistics dashboard', function () {
    $this->seed();

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('Dashboard Statistik Industri')
        ->assertSee('DSI')
        ->assertSee('IBS')
        ->assertSee('KEK/KI');
});
