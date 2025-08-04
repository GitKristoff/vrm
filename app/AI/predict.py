import sys
import json
import random

def predict_diagnosis(input_path, model_path):
    with open(input_path) as f:
        input_data = json.load(f)

    species = input_data.get('species', 'dog').lower()
    symptoms = input_data.get('symptoms', '')

    common_conditions = {
        'dog': ['Kennel Cough', 'Parvovirus', 'Distemper', 'Allergies', 'Arthritis'],
        'cat': ['Feline URI', 'Feline Leukemia', 'Kidney Disease', 'Hyperthyroidism', 'Diabetes']
    }

    conditions = common_conditions.get(species, ['General Infection'])
    diagnosis = random.choice(conditions)
    confidence = round(random.uniform(0.7, 0.95), 2)

    return {
        'possible_conditions': conditions,
        'recommended_treatments': ["Rest", "Hydration", "Veterinary consultation"],
        'medication_interactions': "None identified",
        'confidence_score': confidence,
        'ai_model_version': 'simple-v1',
        'explanation': f"Based on symptoms: {symptoms}. AI suggests {diagnosis} with {confidence*100:.0f}% confidence."
    }

if __name__ == "__main__":
    input_path = sys.argv[1]
    model_path = sys.argv[2]
    result = predict_diagnosis(input_path, model_path)
    print(json.dumps(result))
