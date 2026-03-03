<?php

test('la aplicacion responde correctamente', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
