<?php

use App\Http\Middleware\IsUser;
use App\Http\Middleware\RedirectApprovedAstrologerFromUserSide;
use App\Models\Appointment;
use App\Models\Astrologer;
use App\Models\User;

test('approved astrologer is redirected away from user-facing public and user panel pages', function () {
    /** @var \Tests\TestCase $this */
    $astrologerUser = User::factory()->create();
    $astrologerProfile = Astrologer::create([
        'user_id' => $astrologerUser->id,
        'specialization' => 'Vedic Astrology',
        'experience_years' => 9,
        'bio' => 'Focused on practical guidance and timing.',
        'consultation_fee' => 70,
        'availability_status' => 'available',
        'verification_status' => 'approved',
    ]);

    $this->actingAs($astrologerUser)
        ->get(route('home'))
        ->assertRedirect(route('astrologer.dashboard'));

    $this->actingAs($astrologerUser)
        ->get(route('about'))
        ->assertRedirect(route('astrologer.dashboard'));

    $this->actingAs($astrologerUser)
        ->get(route('services'))
        ->assertRedirect(route('astrologer.dashboard'));

    $this->actingAs($astrologerUser)
        ->get(route('horoscope'))
        ->assertRedirect(route('astrologer.dashboard'));

    $this->actingAs($astrologerUser)
        ->get(route('blog'))
        ->assertRedirect(route('astrologer.dashboard'));

    $this->actingAs($astrologerUser)
        ->get(route('contact'))
        ->assertRedirect(route('astrologer.dashboard'));

    $this->actingAs($astrologerUser)
        ->get(route('dashboard'))
        ->assertRedirect(route('astrologer.dashboard'));

    $this->actingAs($astrologerUser)
        ->get(route('astrologers.index'))
        ->assertRedirect(route('astrologer.dashboard'));

    $this->actingAs($astrologerUser)
        ->get(route('astrologers.show', $astrologerProfile))
        ->assertRedirect(route('astrologer.dashboard'));
});

test('approved astrologer cannot book, rate, review, or open user appointments even when IsUser middleware is bypassed', function () {
    /** @var \Tests\TestCase $this */
    $astrologerUser = User::factory()->create();
    Astrologer::create([
        'user_id' => $astrologerUser->id,
        'specialization' => 'Horary Astrology',
        'experience_years' => 6,
        'bio' => 'Clear answers to specific questions.',
        'consultation_fee' => 60,
        'availability_status' => 'available',
        'verification_status' => 'approved',
    ]);

    $otherOwner = User::factory()->create();
    $otherAstrologer = Astrologer::create([
        'user_id' => $otherOwner->id,
        'specialization' => 'Natal Reading',
        'experience_years' => 11,
        'bio' => 'Long-form readings for career and relationships.',
        'consultation_fee' => 90,
        'availability_status' => 'available',
        'verification_status' => 'approved',
    ]);

    $ownedAppointment = Appointment::create([
        'user_id' => $astrologerUser->id,
        'astrologer_id' => $otherAstrologer->id,
        'scheduled_at' => now()->subDay(),
        'topic' => 'Session follow-up',
        'status' => 'completed',
    ]);

    $this->withoutMiddleware([IsUser::class, RedirectApprovedAstrologerFromUserSide::class])
        ->actingAs($astrologerUser)
        ->post(route('appointments.store', $otherAstrologer), [
            'scheduled_at' => now()->addDays(2)->toDateTimeString(),
            'topic' => 'New session',
            'message' => 'Requesting booking as astrologer.',
        ])
        ->assertForbidden();

    $this->withoutMiddleware([IsUser::class, RedirectApprovedAstrologerFromUserSide::class])
        ->actingAs($astrologerUser)
        ->patch(route('appointments.rate', $ownedAppointment), [
            'rating' => 5,
        ])
        ->assertForbidden();

    $this->withoutMiddleware([IsUser::class, RedirectApprovedAstrologerFromUserSide::class])
        ->actingAs($astrologerUser)
        ->post(route('reviews.store', $otherAstrologer), [
            'appointment_id' => $ownedAppointment->id,
            'rating' => 5,
            'comment' => 'Should not be allowed.',
        ])
        ->assertForbidden();

    $this->withoutMiddleware([IsUser::class, RedirectApprovedAstrologerFromUserSide::class])
        ->actingAs($astrologerUser)
        ->get(route('appointments.user.index'))
        ->assertForbidden();
});
