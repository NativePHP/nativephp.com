<?php

namespace App\Http\Controllers;

class WallOfLoveSubmissionController extends Controller
{
    public function create()
    {
        // Check if user is eligible (has early adopter license)
        $hasEarlyAdopterLicense = auth()->user()
            ->licenses()
            ->where('created_at', '<', '2025-06-01')
            ->exists();

        if (! $hasEarlyAdopterLicense) {
            abort(404);
        }

        // Check if user already has a submission
        $hasExistingSubmission = auth()->user()->wallOfLoveSubmissions()->exists();

        if ($hasExistingSubmission) {
            return redirect()->route('dashboard')->with('info', 'You have already submitted your story to the Wall of Love.');
        }

        return view('customer.wall-of-love.create');
    }
}
