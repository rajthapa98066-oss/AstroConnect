<?php

use App\Models\Appointment;
use App\Models\Astrologer;
use App\Models\User;

test('guests cannot rate completed appointments', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $astrologerOwner = User::factory()->create();
    $astrologer = Astrologer::create([
        'user_id' => $astrologerOwner->id,
        'specialization' => 'Vedic Astrology',
        'experience_years' => 8,
        'bio' => 'Experienced reader for career and timing guidance.',
        'consultation_fee' => 50,
        'availability_status' => 'available',
        'verification_status' => 'approved',
    ]);

    $appointment = Appointment::create([
        'user_id' => $user->id,
        'astrologer_id' => $astrologer->id,
        'scheduled_at' => now()->subDay(),
        'topic' => 'Career direction',
        'status' => 'completed',
    ]);

    $this->patch(route('appointments.rate', $appointment), [
        'rating' => 5,
    ])->assertRedirect(route('login'));
});

test('signed in users can rate a completed appointment they own', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $astrologerOwner = User::factory()->create();
    $astrologer = Astrologer::create([
        'user_id' => $astrologerOwner->id,
        'specialization' => 'Natal Chart Reading',
        'experience_years' => 10,
        'bio' => 'Focused on relationship and career guidance.',
        'consultation_fee' => 75,
        'availability_status' => 'available',
        'verification_status' => 'approved',
    ]);

    $appointment = Appointment::create([
        'user_id' => $user->id,
        'astrologer_id' => $astrologer->id,
        'scheduled_at' => now()->subDay(),
        'topic' => 'Love and timing',
        'status' => 'completed',
    ]);

    $this->actingAs($user)
        ->patch(route('appointments.rate', $appointment), [
            'rating' => 4,
        ])
        ->assertRedirect(route('appointments.user.index'))
        ->assertSessionHasNoErrors();

    $appointment->refresh();

    expect($appointment->rating)->toBe(4);
    expect($appointment->rated_at)->not->toBeNull();
});

test('users cannot rate appointments that are not completed', function () {
    /** @var \Tests\TestCase $this */
    $user = User::factory()->create();
    $astrologerOwner = User::factory()->create();
    $astrologer = Astrologer::create([
        'user_id' => $astrologerOwner->id,
        'specialization' => 'Horary Astrology',
        'experience_years' => 6,
        'bio' => 'Practical guidance for specific questions.',
        'consultation_fee' => 40,
        'availability_status' => 'available',
        'verification_status' => 'approved',
    ]);

    $appointment = Appointment::create([
        'user_id' => $user->id,
        'astrologer_id' => $astrologer->id,
        'scheduled_at' => now()->addDay(),
        'topic' => 'Timing question',
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->patch(route('appointments.rate', $appointment), [
            'rating' => 5,
        ])
        ->assertSessionHasErrors('rating');
});
