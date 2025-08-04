<?php

namespace App\Http\Controllers;

use App\Models\AIDiagnosis;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AIController extends Controller
{
    public function predict(Request $request)
    {
        $request->validate([
            'symptoms' => 'required|string',
            'pet_id' => 'required|exists:pets,id'
        ]);

        try {
            $pet = Pet::find($request->pet_id);

            // For testing purposes - returns mock data
            // return $this->mockAIPrediction($request->symptoms, $pet);

            // For production use - uncomment this line:
            return $this->runPythonPrediction($request->symptoms, $pet);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'AI prediction failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate mock AI prediction for testing
     */
    private function mockAIPrediction($symptoms, Pet $pet)
    {
        $commonConditions = [
            'dog' => ['Kennel Cough', 'Parvovirus', 'Distemper', 'Allergies', 'Arthritis'],
            'cat' => ['Feline URI', 'Feline Leukemia', 'Kidney Disease', 'Hyperthyroidism', 'Diabetes']
        ];

        $species = strtolower($pet->species);
        $conditions = $commonConditions[$species] ?? ['General Infection'];
        $primaryCondition = $conditions[array_rand($conditions)];
        $confidence = round(rand(70, 95) / 100, 2);

        return response()->json([
            'diagnosis' => $primaryCondition,
            'confidence' => $confidence,
            'additional_notes' => "Based on symptoms: $symptoms. This is a mock diagnosis.",
            'possible_conditions' => $conditions,
            'recommended_treatments' => ["Rest", "Hydration", "Veterinary consultation"],
            'medication_interactions' => "None identified",
            'confidence_score' => $confidence,
            'ai_model_version' => 'mock-v1',
            'explanation' => "This is a preliminary mock diagnosis. Always consult a veterinarian for professional medical advice."
        ]);
    }

    /**
     * Run actual Python prediction script
     */
    private function runPythonPrediction($symptoms, Pet $pet)
    {
        $medicalHistory = $pet->medicalRecords->pluck('summary')->implode('\n');

        // Prepare input data
        $inputData = [
            'symptoms' => $symptoms,
            'medical_history' => $medicalHistory,
            'species' => $pet->species,
            'age' => $pet->age,
        ];

        // Save input to temp file
        $inputPath = storage_path('app/ai_input_'.time().'.json');
        file_put_contents($inputPath, json_encode($inputData));

        // Run Python prediction script
        $process = new Process([
            base_path('tf_env/Scripts/python.exe'),
            app_path('AI/predict.py'),
            $inputPath,
            app_path('AI/symptom_checker_model.h5')
        ]);
        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = json_decode($process->getOutput(), true);

        // Clean up input file
        if (file_exists($inputPath)) {
            unlink($inputPath);
        }

        // Save diagnosis to database
        $diagnosis = AIDiagnosis::create([
            'medical_record_id' => $pet->medicalRecords->first()?->id,
            'symptoms' => $symptoms,
            'possible_conditions' => json_encode($output['possible_conditions']),
            'recommended_treatments' => json_encode($output['recommended_treatments']),
            'medication_interactions' => json_encode($output['medication_interactions'] ?? []),
            'confidence_score' => $output['confidence_score'],
            'ai_model_version' => $output['ai_model_version'] ?? 'v1',
            'explanation' => $output['explanation'] ?? null
        ]);

        // Format response for frontend
        return response()->json([
            'diagnosis' => $output['possible_conditions'][0] ?? 'Unknown',
            'confidence' => $output['confidence_score'],
            'additional_notes' => $output['explanation'] ?? 'No additional notes',
            'possible_conditions' => $output['possible_conditions'],
            'recommended_treatments' => $output['recommended_treatments'],
            'medication_interactions' => $output['medication_interactions'] ?? 'None identified',
            'confidence_score' => $output['confidence_score'],
            'ai_model_version' => $output['ai_model_version'] ?? 'v1',
            'explanation' => $output['explanation'] ?? 'No explanation available'
        ]);
    }
}
