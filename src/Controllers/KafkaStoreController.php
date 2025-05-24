<?php

namespace KafkaStore\LibrenmsKafkaStorePlugin\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class KafkaStoreController extends Controller
{
    public function saveConfig(Request $request): RedirectResponse
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'kafka_enabled' => 'boolean',
                'kafka_brokers' => 'required|string',
                'kafka_device_fields' => 'nullable|string',
                'kafka_device_groups' => 'nullable|string',
                'kafka_device_measurements' => 'nullable|string',
                'kafka_topic' => 'required|string',
                'kafka_idempotency' => 'boolean',
                'kafka_ssl_enabled' => 'boolean',
                'kafka_ssl_keystore_location' => 'nullable|string',
                'kafka_ssl_keystore_password' => 'nullable|string',
                'kafka_batch_size' => 'integer|min:1',
                'kafka_buffer_max_size' => 'integer|min:1',
                'kafka_flush_timeout' => 'integer|min:0',
                'kafka_linger' => 'integer|min:0',
                'kafka_required_ack' => 'integer|in:-1,0',
                'kafka_debug_enabled' => 'boolean',
                'kafka_security_debug' => 'nullable|string',
            ]);
            // Logic to save configuration settings
            // TODO: Implement actual configuration saving logic here

            // Redirect back with success message
            return redirect()->back()->with('status', 'Configuration saved successfully!');
        } catch (ValidationException $e) {
            // Validation failed - Laravel automatically redirects back with errors
            // But we can also manually handle it if needed
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors below.');
        } catch (\Exception $e) {
            // Handle any other exceptions
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while saving the configuration: '.$e->getMessage());
        }
    }
}
