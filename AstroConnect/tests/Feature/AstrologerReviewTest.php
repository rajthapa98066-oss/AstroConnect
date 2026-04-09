<?php

use App\Models\Astrologer;
use App\Models\Appointment;
use App\Models\Review;
use App\Models\User;

test('guests can view astrologer reviews but cannot submit one', function () {
    /** @var \Tests\TestCase $this */
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

    $reviewer = User::factory()->create();
    Review::create([
        'user_id' => $reviewer->id,
        'astrologer_id' => $astrologer->id,
        'rating' => 5,
        'comment' => 'Insightful and accurate.',
    ]);

    $this->get(route('astrologers.show', $astrologer))
        ->assertOk()
        ->assertSee('Insightful and accurate.');

    $this->post(route('reviews.store', $astrologer), [
        'rating' => 4,
        'comment' => 'Looks good.',
    ])->assertRedirect(route('login'));
});

test('signed in users can create and update a review for an approved astrologer', function () {
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
        'scheduled_at' => now()->subHour(),
        'topic' => 'Career reading',
        'status' => 'completed',
    ]);

    $this->actingAs($user)
        ->post(route('reviews.store', $astrologer), [
            'appointment_id' => $appointment->id,
            'rating' => 4,
            'comment' => 'Very helpful session.',
        ])
        ->assertRedirect(route('astrologers.show', $astrologer))
        ->assertSessionHasNoErrors();

    $review = Review::query()->where('user_id', $user->id)->where('astrologer_id', $astrologer->id)->first();

    expect($review)->not->toBeNull();
    expect($review?->rating)->toBe(4);
    expect($review?->comment)->toBe('Very helpful session.');

    $this->actingAs($user)
        ->post(route('reviews.store', $astrologer), [
            'appointment_id' => $appointment->id,
            'rating' => 5,
            'comment' => 'Updated after a follow-up reading.',
        ])
        ->assertRedirect(route('astrologers.show', $astrologer))
        ->assertSessionHasNoErrors();

    $review->refresh();

    expect($review->rating)->toBe(5);
    expect($review->comment)->toBe('Updated after a follow-up reading.');

    $appointment->refresh();
    expect($appointment->rating)->toBe(5);
    expect($appointment->rated_at)->not->toBeNull();
});

test('signed in users cannot review astrologer before appointment is completed', function () {
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
        'scheduled_at' => now()->addDay(),
        'topic' => 'Relationship reading',
        'status' => 'pending',
    ]);

    $this->actingAs($user)
        ->post(route('reviews.store', $astrologer), [
            'appointment_id' => $appointment->id,
            'rating' => 4,
            'comment' => 'Trying to review too early.',
        ])
        ->assertSessionHasErrors('comment');
});
