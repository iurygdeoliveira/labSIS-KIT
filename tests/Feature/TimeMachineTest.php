<?php

test('time machine dashboard is accessible when enabled', function () {
    config(['time-machine.enabled' => true]);

    $response = $this->get('/time-machine');

    $response->assertStatus(200);
});
