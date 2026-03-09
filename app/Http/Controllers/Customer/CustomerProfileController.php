<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerProfileController extends Controller
{
    /**
     * Show profile completion form.
     */
    public function complete()
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->first();

        // Check if profile is already complete
        if ($customer && $customer->profile) {
            return redirect()->route('customer.shop');
        }

        return view('customer.profile.complete', compact('customer'));
    }

    /**
     * Store profile information.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'business_type' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $customer = Customer::where('user_id', $user->id)->first();

            if (!$customer) {
                throw new \Exception('Customer record not found.');
            }

            // Auto-geocode address if lat/lng not provided via map click
            if (empty($validated['latitude']) || empty($validated['longitude'])) {
                $coords = $this->geocodeAddress($validated['address']);
                $validated['latitude']  = $coords['lat'];
                $validated['longitude'] = $coords['lng'];
            }

            // Update customer info (use existing user name as contact person)
            $customer->update([
                'business_name' => $validated['company_name'],
                'contact_person' => $user->name,
                'phone' => $validated['contact_number'],
                'address' => $validated['address'],
                'customer_type' => $validated['business_type'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);

            // Create or update customer profile
            CustomerProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $validated['company_name'],
                    'business_type' => $validated['business_type'],
                    'contact_person_name' => $user->name,
                    'phone' => $validated['contact_number'],
                    'address' => $validated['address'],
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                    'profile_completed' => true,
                ]
            );

            DB::commit();

            return redirect()->route('customer.shop')
                ->with('success', 'Profile completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to save profile: ' . $e->getMessage());
        }
    }

    /**
     * Show profile page.
     */
    public function show()
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->with('profile')->first();

        return view('customer.profile.show', compact('customer'));
    }

    /**
     * Show profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->with('profile')->first();
        
        // Debug: log customer data
        \Log::info('Edit Profile - Customer Data', [
            'customer_type' => $customer->customer_type ?? 'NULL',
            'latitude' => $customer->latitude ?? 'NULL',
            'longitude' => $customer->longitude ?? 'NULL',
            'has_customer' => $customer ? 'yes' : 'no'
        ]);

        return view('customer.profile.edit', compact('customer'));
    }

    /**
     * Update profile information.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'business_type' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $user = Auth::user();
            
            // Update user name if changed
            if ($user->name !== $validated['name']) {
                $user->update(['name' => $validated['name']]);
            }
            
            $customer = Customer::where('user_id', $user->id)->first();

            // Auto-geocode address if lat/lng not provided via map click
            if (empty($validated['latitude']) || empty($validated['longitude'])) {
                $coords = $this->geocodeAddress($validated['address']);
                $validated['latitude']  = $coords['lat'];
                $validated['longitude'] = $coords['lng'];
            }

            // Update customer info
            $customer->update([
                'business_name' => $validated['company_name'],
                'contact_person' => $validated['name'],
                'phone' => $validated['contact_number'],
                'address' => $validated['address'],
                'customer_type' => $validated['business_type'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
            ]);

            // Update profile
            $profile = CustomerProfile::where('user_id', $user->id)->first();
            if ($profile) {
                $profile->update([
                    'company_name' => $validated['company_name'],
                    'business_type' => $validated['business_type'],
                    'contact_person_name' => $validated['name'],
                    'phone' => $validated['contact_number'],
                    'address' => $validated['address'],
                    'latitude' => $validated['latitude'],
                    'longitude' => $validated['longitude'],
                ]);
            }

            DB::commit();

            return redirect()->route('customer.profile.show')
                ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update profile: ' . $e->getMessage());
        }
    }

    /**
     * Geocode an address string to lat/lng using Nominatim.
     * Returns ['lat' => null, 'lng' => null] if geocoding fails.
     */
    private function geocodeAddress(string $address): array
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'User-Agent' => 'VNF-ColdStorage/1.0',
            ])->timeout(5)->get('https://nominatim.openstreetmap.org/search', [
                'q'      => $address,
                'format' => 'json',
                'limit'  => 1,
            ]);

            $results = $response->json();
            if (!empty($results[0])) {
                return [
                    'lat' => (float) $results[0]['lat'],
                    'lng' => (float) $results[0]['lon'],
                ];
            }
        } catch (\Exception $e) {
            \Log::warning('Geocoding failed: ' . $e->getMessage());
        }

        return ['lat' => null, 'lng' => null];
    }
}
