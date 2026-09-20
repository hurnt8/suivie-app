<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use App\Models\Shipment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class TrackingController extends Controller
{
    public function search(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:32'],
        ]);

        $code = Str::of($data['code'])->trim()->upper()->toString();

        if (! Shipment::where('tracking_code', $code)->exists()) {
            throw ValidationException::withMessages([
                'code' => __('messages.tracking_not_found'),
            ])->redirectTo(route('home').'#track');
        }

        return redirect()->route('tracking.show', $code);
    }

    public function show(Shipment $shipment): View
    {
        $shipment->load(['sender', 'recipient', 'events' => fn ($query) => $query->orderBy('event_date')]);

        return view('public.tracking.show', [
            'shipment' => $shipment,
            'settings' => Settings::current(),
        ]);
    }
}
