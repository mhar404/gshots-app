<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Product;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $userMessage = $request->input('message');
        $apiKey = config('services.gemini.key');

        \Log::info('Gemini API Key Presence: ' . ($apiKey ? 'Present' : 'Missing'));

        if (!$apiKey) {
            return response()->json(['error' => 'Gemini API key not configured.'], 500);
        }

        // 1. Fetch menu context
        try {
            $products = Product::all(['name', 'description', 'price', 'category', 'is_available']);
        } catch (\Exception $e) {
            \Log::error('Chat Database Error: ' . $e->getMessage());
            $products = collect();
        }

        $menuContext = "Store Name: Gshot\n";
        $menuContext .= "Products Menu:\n";
        foreach ($products as $product) {
            $status = $product->is_available ? 'Available' : 'Not Available';
            $menuContext .= "- {$product->name} [Status: {$status}] ({$product->category}): {$product->description}. Price: ₱{$product->price}\n";
        }

        // 2. Prepare the prompt
        $systemPrompt = "You are 'Gshot Assistant', a friendly and helpful AI assistant for Gshot, a beverage/milk tea shop. 
        Use the following menu context to answer customer questions accurately. 
        If a customer asks for a recommendation, suggest something based on the menu. 
        IMPORTANT: Pay attention to the [Status] of products. DO NOT recommend products that are 'Not Available'. If they ask for a 'Not Available' item, politely inform them it's currently out of stock and suggest an 'Available' alternative.
        Keep your responses concise, friendly, and use emojis occasionally. 
        If they ask something unrelated to Gshots or beverages, politely refocus them on the menu.

        // Add your custom rule here:
        If a customer asks about 'owner of gshot', you must respond  'Raymond 'Badingdong' Barajas'.
        If a customer asks about 'location of gshot', you must respond  'BLOCK 9 LOT 3, AMIRA TOWNHOMES, Brgy Sinalhan, City of Santa Rosa, Laguna'.
        If a customer asks about 'store opening hours and days', you must respond  'Monday to Sunday, 09:00 AM to 10:00 PM'.

        Menu Context:
        {$menuContext}";

        // 3. Call Gemini API (Using stable 2026 models: 2.5-flash and persistent aliases)
        $models = ['gemini-2.5-flash', 'gemini-flash-latest', 'gemini-2.0-flash'];
        $aiResponse = null;
        $lastError = null;

        foreach ($models as $model) {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [['text' => $systemPrompt . "\n\nCustomer: " . $userMessage]]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1000,
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($aiResponse)
                    break;
            } else {
                $lastError = $response->json();
                \Log::warning("Gemini Model {$model} failed: " . ($lastError['error']['message'] ?? 'Unknown Error'));
            }
        }

        if (!$aiResponse) {
            \Log::error('All Gemini Models Failed:', $lastError ?? []);
            return response()->json([
                'error' => 'AI Service Error',
                'details' => $lastError['error']['message'] ?? 'Could not get response from AI'
            ], 500);
        }






        $result = $response->json();
        $aiResponse = $result['candidates'][0]['content']['parts'][0]['text'] ?? "I'm sorry, I couldn't process that request.";

        return response()->json([
            'message' => $aiResponse,
        ]);
    }
}
