<?php

test('home page redirects to events index', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect(route('events.index'));
});
