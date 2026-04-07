<?php

// Basic feature sanity check for home route availability.
it('returns a successful response', function () {
    /** @var \Tests\TestCase $this */
    $response = $this->get('/');

    $response->assertStatus(200);
});
