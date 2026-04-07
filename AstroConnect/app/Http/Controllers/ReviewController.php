<?php

namespace App\Http\Controllers;

use App\Models\Astrologer;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store or update a review for an approved astrologer.
     */
    public function store(Request $request, Astrologer $astrologer): RedirectResponse
    {
        abort_if($astrologer->verification_status !== 'approved', 404);
        abort_unless($request->user()->role === 'user', 403);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        Review::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'astrologer_id' => $astrologer->id,
            ],
            [
                'rating' => $validated['rating'],
                'comment' => $validated['comment'],
            ]
        );

        return redirect()
            ->route('astrologers.show', $astrologer)
            ->with('status', 'review-saved');
    }
}
