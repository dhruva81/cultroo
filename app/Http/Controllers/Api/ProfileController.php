<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvatarResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\SeriesResource;
use App\Models\Avatar;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * List profiles
     *
     * This endpoint will send authenticated user's active profiles list (which are not archived!).
     */
    public function index()
    {
        $profiles = auth()->user()->profiles;

        return ProfileResource::collection($profiles);
    }

    /**
     * Show profile
     *
     * This endpoint will send a profile data.
     */
    public function show(Profile $profile)
    {
        return new ProfileResource($profile);
    }

    /**
     * Show active profile
     *
     * This endpoint will send authenticated user's active profile data.
     */
    public function activeProfile()
    {
        $profile = auth()->user()->getActiveProfile();

        if ($profile) {
            return ProfileResource::make($profile);
        }

        return response()->json([
            'message' => 'No active profile exists',
        ], 200);
    }

    /**
     * Create a profile
     *
     * This endpoint will create a new profile for authenticated user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'dob' => ['required', 'date'],
        ]);

        $profile = auth()->user()->profiles()->create([
            'name' => $request->name,
            'dob' => $request->dob,
        ]);

        activity()
            ->performedOn($profile)
            ->event('created')
            ->log('a new profile');

        return response()->json([
            'message' => 'Profile created successfully!',
            'profile' => ProfileResource::make($profile),
        ], 201);
    }

    /**
     * Update a profile
     *
     * This endpoint will update a profile.
     */
    public function update(Request $request, Profile $profile)
    {
        if ($profile->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to set preferences for this profile.',
            ], 403);
        }

        if($request->has('name') && $request->name !== null) {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
            ]);

            $profile->name = $request->name;
        }

        if($request->has('dob') && $request->dob !== null) {
            $request->validate([
                'dob' => ['required', 'date'],
            ]);

            $profile->dob = $request->dob;
        }

        if($request->has('avatar_id') && $request->avatar_id !== null) {
            $request->validate([
                'avatar_id' => ['required', 'exists:avatars,id'],
            ]);

            $profile->avatar_id = $request->avatar_id;
        }

        if ($request->has('languages') && request('languages') !== null) {
            $profile->languages()->sync($request['languages']);
        }

        if ($request->has('genres') && request('genres') !== null) {
            $profile->genres()->sync($request['genres']);
        }


        $profile->save();

        activity()
            ->performedOn($profile)
            ->event('updated')
            ->log('profile');

        return response()->json([
            'message' => 'Profile updated successfully!',
        ], 201);
    }

    /**
     * Delete a profile
     *
     * This endpoint will delete a profile. This step is irreversible.
     */
    public function delete(Profile $profile)
    {
        $profile->delete();

        return response()->json([
            'message' => 'Profile deleted successfully!',
        ], 204);
    }

    // TODO - This method is deprecated
    /**
     * Set preferences
     *
     * This endpoint will set preferences for a profile. This endpoint is deprecated. Please use update profile endpoint instead.
     */
    public function preferences(Request $request, Profile $profile)
    {
        if ($profile->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to set preferences for this profile.',
            ], 403);
        }

        if ($request->has('languages')) {
            $profile->languages()->sync($request['languages']);
        }

        if ($request->has('genres')) {
            $profile->genres()->sync($request['genres']);
        }

        return response()->json([
            'message' => 'Profile preferences updated successfully!',
        ], 201);
    }

    /**
     * Activate a profile
     *
     * This endpoint will activate a profile.
     */
    public function activateProfile(Profile $profile)
    {
        if ($profile->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized to switch to this profile',
            ], 403);
        }

        auth()->user()->update([
            'active_profile_id' => $profile->id,
        ]);

        return response()->json([
            'message' => 'Profile activated successfully!',
        ], 201);
    }

    /**
     * List avatars
     *
     * This endpoint will send avatars list.
     */
    public function avatars()
    {
        $avatars = Avatar::whereNotNull('avatar_path')->paginate(50);
        return AvatarResource::collection($avatars);
    }

    /**
     * Toggle Search History
     *
     * This endpoint will toggle search history for a profile. If tracking of search history is enabled, it will be disabled and vice versa.
     */
    public function toggleSearchHistory(Profile $profile)
    {
        if ($profile->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized for this action.',
            ], 403);
        }

        $profile->update([
            'tracking_search_history' => ! $profile->tracking_search_history,
        ]);

        return response()->json([
            'message' => 'success',
        ], 201);
    }

    /**
     * Toggle Watch History
     *
     * This endpoint will toggle watch history for a profile. If tracking of watch history is enabled, it will be disabled and vice versa.
     */
    public function toggleWatchHistory(Profile $profile)
    {
        if ($profile->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized for this action.',
            ], 403);
        }

        $profile->update([
            'tracking_watch_history' => ! $profile->tracking_watch_history,
        ]);

        return response()->json([
            'message' => 'success',
        ], 201);
    }

    /**
     * Clear History
     *
     * This endpoint will clear search and watch history for a profile.
     */
    public function clearHistory(Profile $profile)
    {
        if ($profile->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized for this action.',
            ], 403);
        }

        $profile->searches->each->update([
            'is_visible' => false
        ]);

        $profile->watchHistories->each->update([
            'is_visible' => false
        ]);

        return response()->json([
            'message' => 'success',
        ], 201);
    }

    /**
     * Set profile pin
     *
     * This endpoint is used to set a pin for a profile. It can be used for updating the pin as well.
     */
    public function setProfilePin(Request $request)
    {
        $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'pin' => ['required', 'integer', 'digits:4'],
        ]);

        $profile = Profile::find($request->profile_id);

        if ($profile->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized for this action.',
            ], 403);
        }

        $profile->update([
            'pin' => $request->pin,
        ]);

        return response()->json([
            'message' => 'Profile pin set successfully!',
        ], 201);
    }

    /**
     * Verify profile pin
     *
     * This endpoint is used to verify a pin for a profile.
     * If the pin is correct, then response code will be 200.
     * For incorrect pin, response code will be 422.
     */
    public function verifyProfilePin(Request $request)
    {
        $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'pin' => ['required', 'integer', 'digits:4'],
        ]);

        $profile = Profile::find($request->profile_id);

        if ($profile->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized for this action.',
            ], 403);
        }

        if ($profile->pin != $request->pin) {
            throw ValidationException::withMessages([
                'pin' => ['Invalid profile pin.'],
            ]);
        }

        return response()->json([
            'message' => 'Profile pin verified successfully!',
        ], 200);
    }

    /**
     * Clear profile pin
     *
     * This endpoint is used to clear the pin for a profile.
     */
    public function clearProfilePin(Request $request)
    {
        $request->validate([
            'profile_id' => ['required', 'exists:profiles,id']
        ]);

        $profile = Profile::find($request->profile_id);

        if ($profile->user_id !== auth()->user()->id) {
            return response()->json([
                'message' => 'You are not authorized for this action.',
            ], 403);
        }

        $profile->update([
            'pin' => null,
        ]);

        return response()->json([
            'message' => 'Profile pin cleared successfully!',
        ], 201);
    }


}
