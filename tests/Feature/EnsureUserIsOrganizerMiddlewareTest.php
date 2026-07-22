<?php

use App\Http\Middleware\EnsureUserIsOrganizer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

test('organizer can pass through the middleware', function () {
    $organizer = User::factory()->organizer()->create();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn () => $organizer);

    $response = (new EnsureUserIsOrganizer)->handle($request, function () {
        return new Response('OK');
    });

    expect($response->getStatusCode())->toBe(200);
});

test('attendee is blocked by the middleware', function () {
    $attendee = User::factory()->create();

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn () => $attendee);

    (new EnsureUserIsOrganizer)->handle($request, function () {
        return new Response('OK');
    });
})->throws(HttpException::class);

test('guest is blocked by the middleware', function () {
    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn () => null);

    (new EnsureUserIsOrganizer)->handle($request, function () {
        return new Response('OK');
    });
})->throws(HttpException::class);
