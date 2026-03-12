<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AdminAstrologerController extends Controller
{
    public function index(): View
    {
        $astrologers = Astrologer::with('user')->latest()->paginate(20);

        return view('pages.admin.astrologers.index', [
            'astrologers' => $astrologers,
        ]);
    }

    public function approve(Astrologer $astrologer): RedirectResponse
    {
        $astrologer->update(['verification_status' => 'approved']);

        return Redirect::route('admin.astrologers.index')->with('status', 'astrologer-approved');
    }

    public function reject(Astrologer $astrologer): RedirectResponse
    {
        $astrologer->update(['verification_status' => 'rejected']);

        return Redirect::route('admin.astrologers.index')->with('status', 'astrologer-rejected');
    }

    public function update(Request $request, Astrologer $astrologer): RedirectResponse
    {
        $validated = $request->validate([
            'specialization' => ['required', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:80'],
            'consultation_fee' => ['required', 'numeric', 'min:0'],
            'availability_status' => ['required', 'in:available,busy,unavailable'],
            'verification_status' => ['required', 'in:pending,approved,rejected'],
        ]);

        $astrologer->update($validated);

        return Redirect::route('admin.astrologers.index')->with('status', 'astrologer-updated');
    }
}
