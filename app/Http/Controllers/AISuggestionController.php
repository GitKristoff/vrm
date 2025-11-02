<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AISuggestionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * POST /ai/predict
     * body: { symptoms: string, pet_id?: int }
     */
    public function predict(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|string',
            'pet_id' => 'nullable|exists:pets,id',
        ]);

        $symptoms = $request->input('symptoms');
        $pet = null;
        if ($request->filled('pet_id')) {
            $pet = \App\Models\Pet::find($request->input('pet_id'));
        }

        $petInfo = '';
        if ($pet) {
            $petInfo = "Pet name: {$pet->name}. Species: {$pet->species}. Age: {$pet->age}. Weight: {$pet->weight}\n";
        }

        // Build a prompt that asks the model to return strictly JSON
        $prompt = "You are a veterinary assistant. Given the owner's symptoms and optional pet info, return a JSON object only (no extra text) with the following keys:\n"
            . "- diagnosis: a short primary diagnosis or best guess (string)\n"
            . "- confidence: a number between 0 and 1 representing confidence (float)\n"
            . "- additional_notes: short notes and caveats (string)\n"
            . "- possible_conditions: an array of short possible conditions (array of strings)\n"
            . "- recommended_treatments: an array of short treatment recommendations or next steps (array of strings)\n\n"
            . "Input:\n"
            . $petInfo
            . "Symptoms: " . $symptoms . "\n\n"
            . "Important: ONLY return valid JSON (no surrounding backticks or explanatory text). Keep fields concise. Include a short safety disclaimer inside additional_notes if necessary.";

        $apiKey = config('services.openai.key') ?? env('OPENAI_API_KEY');
        $model = config('services.openai.model') ?? env('OPENAI_CHAT_MODEL', 'gpt-4o-mini');

        try {
            $resp = Http::withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a professional veterinary assistant. Answer as JSON only.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.2,
                    'max_tokens' => 400,
                    'top_p' => 1,
                ]);

            if (! $resp->successful()) {
                Log::error('AI Suggestion failed', ['status' => $resp->status(), 'body' => $resp->body()]);
                return response()->json(['error' => 'AI service error'], 500);
            }

            $json = $resp->json();
            $reply = $json['choices'][0]['message']['content'] ?? '';

            // Try direct JSON decode
            $data = json_decode($reply, true);

            // If direct decode fails, attempt to extract first JSON object substring
            if ($data === null) {
                if (preg_match('/(\{(?:[^{}]|(?R))*\})/s', $reply, $m)) {
                    $maybe = $m[1];
                    $decoded = json_decode($maybe, true);
                    if ($decoded !== null) {
                        $data = $decoded;
                    }
                }
            }

            // If still not JSON, return raw reply in 'raw' field
            if ($data === null) {
                return response()->json([
                    'raw' => $reply,
                    'message' => 'AI returned non-JSON. See raw for contents.'
                ]);
            }

            // Normalize expected fields with safe defaults
            $responsePayload = [
                'diagnosis' => $data['diagnosis'] ?? null,
                'confidence' => isset($data['confidence']) ? (float) $data['confidence'] : null,
                'additional_notes' => $data['additional_notes'] ?? null,
                'possible_conditions' => is_array($data['possible_conditions']) ? $data['possible_conditions'] : (isset($data['possible_conditions']) ? [$data['possible_conditions']] : []),
                'recommended_treatments' => is_array($data['recommended_treatments']) ? $data['recommended_treatments'] : (isset($data['recommended_treatments']) ? [$data['recommended_treatments']] : []),
            ];

            return response()->json($responsePayload);
        } catch (\Exception $e) {
            Log::error('AI Suggestion exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Exception: ' . $e->getMessage()], 500);
        }
    }
}
