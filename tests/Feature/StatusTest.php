<?php

it('has status page', function () {
    $response = $this->get('/status')->assertStatus(200)->json();

    expect($response)->toMatchArray(['ok' => true]);
});
