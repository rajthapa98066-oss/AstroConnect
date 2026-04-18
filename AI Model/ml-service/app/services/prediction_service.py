import os
import joblib
import pandas as pd
from pathlib import Path

# Need to import constants from preprocessing to avoid code duplication
import sys
BASE_DIR = Path(__file__).resolve().parent.parent.parent
sys.path.insert(0, str(BASE_DIR))
from preprocessing.step4_feature_engineering import (
    ZODIAC_ORDER, SIGN_ELEMENT_MAP, SIGN_QUALITY_MAP,
    COMPATIBLE_PAIRS, NAKSHATRA_ORDER, NAKSHATRA_GANA_MAP,
    get_circular_distance, is_highly_compatible
)

class PredictionService:
    def __init__(self):
        self.models_dir = BASE_DIR / "models"
        self.career_confidence_threshold = 0.50
        self.career_margin_threshold = 0.12
        
        # 1. Load Compatibility Models & Encoders
        self.compat_model = joblib.load(self.models_dir / "compatibility_model.pkl")
        self.moon_sign_encoder = joblib.load(self.models_dir / "moon_sign_encoder.pkl")
        self.nakshatra_encoder = joblib.load(self.models_dir / "nakshatra_encoder.pkl")
        self.compat_features = joblib.load(self.models_dir / "compatibility_features.pkl")
        
        # 2. Load Career Models & Encoders
        self.career_model = joblib.load(self.models_dir / "career_model.pkl")
        self.career_target_encoder = joblib.load(self.models_dir / "encoders" / "career_target_encoder.pkl")
        self.career_feature_order = [
            'sun_sign', 'moon_sign', 'mars_sign', 'mercury_sign',
            'jupiter_sign', 'venus_sign', 'saturn_sign',
            'rahu_sign', 'ketu_sign'
        ]

        career_features_path = self.models_dir / "career_features.pkl"
        if career_features_path.exists():
            self.career_feature_order = joblib.load(career_features_path)
        
        # We need to dynamically load all planetary sign encoders
        self.career_encoders = {}
        for planet in ['sun', 'moon', 'mars', 'mercury', 'jupiter', 'venus', 'saturn', 'rahu', 'ketu']:
            encoder_path = self.models_dir / "encoders" / f"career_{planet}_sign_encoder.pkl"
            self.career_encoders[f"{planet}_sign"] = joblib.load(encoder_path)

    @staticmethod
    def _normalize_sign(sign: str) -> str:
        """Normalize user-provided sign strings to model-trained title case."""
        return str(sign).strip().title()

    def predict_compatibility(self, data: dict) -> dict:
        """
        Receives raw JSON format, performs Step 4 & 5 feature engineering inline,
        then feeds it into the Random Forest prediction model.
        """
        # Feature Engineering (Step 4 logic)
        sign1, sign2 = data['person1_moon_sign'], data['person2_moon_sign']
        nak1, nak2 = data['person1_nakshatra'], data['person2_nakshatra']
        
        sign_idx_1 = ZODIAC_ORDER.index(sign1)
        sign_idx_2 = ZODIAC_ORDER.index(sign2)
        sign_distance = get_circular_distance(sign_idx_1, sign_idx_2, 12)
        sign_element_match = 1 if SIGN_ELEMENT_MAP[sign1] == SIGN_ELEMENT_MAP[sign2] else 0
        sign_quality_match = 1 if SIGN_QUALITY_MAP[sign1] == SIGN_QUALITY_MAP[sign2] else 0
        
        sign_compatibility = is_highly_compatible(sign1, sign2)
        
        nak_idx_1 = NAKSHATRA_ORDER.index(nak1)
        nak_idx_2 = NAKSHATRA_ORDER.index(nak2)
        nakshatra_distance = get_circular_distance(nak_idx_1, nak_idx_2, 27)
        
        nakshatra_group_match = 1 if NAKSHATRA_GANA_MAP[nak1] == NAKSHATRA_GANA_MAP[nak2] else 0

        # Build feature dictionary
        features = {
            'person1_moon_sign': self.moon_sign_encoder.transform([sign1])[0],
            'person1_nakshatra': self.nakshatra_encoder.transform([nak1])[0],
            'person2_moon_sign': self.moon_sign_encoder.transform([sign2])[0],
            'person2_nakshatra': self.nakshatra_encoder.transform([nak2])[0],
            'varna_score': data['varna_score'],
            'vashya_score': data['vashya_score'],
            'tara_score': data['tara_score'],
            'yoni_score': data['yoni_score'],
            'graha_maitri_score': data['graha_maitri_score'],
            'gana_score': data['gana_score'],
            'bhakoot_score': data['bhakoot_score'],
            'nadi_score': data['nadi_score'],
            'sign_distance': sign_distance,
            'sign_element_match': sign_element_match,
            'sign_quality_match': sign_quality_match,
            'sign_compatibility': sign_compatibility,
            'nakshatra_distance': nakshatra_distance,
            'nakshatra_group_match': nakshatra_group_match
        }
        
        # Order them exactly as expected
        ordered_features = [features[f] for f in self.compat_features]
        
        # Predict
        prediction = self.compat_model.predict([ordered_features])[0]
        prob = self.compat_model.predict_proba([ordered_features])[0]
        
        return {
            "prediction": "Highly Compatible" if prediction == 1 else "Not Compatible",
            "is_compatible": bool(prediction),
            "confidence_score": float(prob[1] if prediction == 1 else prob[0])
        }

    def predict_career(self, data: dict) -> dict:
        """
        Receives 9 planetary signs, applies consistent encoding,
        predicts via model, and returns the top class with confidence.
        """
        encoded_row = {}
        for feature in self.career_feature_order:
            if feature not in data:
                raise ValueError(f"Missing required feature: {feature}")

            encoder = self.career_encoders[feature]
            normalized_sign = self._normalize_sign(data[feature])

            if normalized_sign not in encoder.classes_:
                allowed = ", ".join(sorted(map(str, encoder.classes_)))
                raise ValueError(
                    f"Invalid value '{data[feature]}' for {feature}. Allowed values: {allowed}"
                )

            encoded_row[feature] = int(encoder.transform([normalized_sign])[0])

        input_df = pd.DataFrame([encoded_row], columns=self.career_feature_order)
        prediction = self.career_model.predict(input_df)[0]
        # Ensure we have a native Python int for the encoder
        if hasattr(prediction, 'item'):
            prediction = prediction.item()
        prediction = int(prediction)
        
        career_label = str(self.career_target_encoder.inverse_transform([prediction])[0])

        response = {
            "predicted_career": career_label
        }

        # Include probability diagnostics when available to make low-confidence outputs visible.
        if hasattr(self.career_model, "predict_proba"):
            probabilities = self.career_model.predict_proba(input_df)[0]
            class_ids = self.career_model.classes_

            ranked = sorted(
                [
                    {
                        "career": str(self.career_target_encoder.inverse_transform([int(class_id.item() if hasattr(class_id, "item") else class_id)])[0]),
                        "probability": float(prob.item() if hasattr(prob, "item") else prob),
                    }
                    for class_id, prob in zip(class_ids, probabilities)
                ],
                key=lambda x: x["probability"],
                reverse=True,
            )

            response["confidence_score"] = float(ranked[0]["probability"])
            response["top_predictions"] = ranked[:3]

            if len(ranked) > 1:
                response["prediction_margin"] = float(ranked[0]["probability"] - ranked[1]["probability"])

            margin = float(response.get("prediction_margin", ranked[0]["probability"]))
            confidence = float(response["confidence_score"])
            uncertain = confidence < self.career_confidence_threshold or margin < self.career_margin_threshold
            response["is_uncertain"] = uncertain

            if uncertain:
                response["predicted_career"] = "Uncertain"
                response["suggested_career"] = ranked[0]["career"]
                response["uncertainty_reason"] = (
                    f"Low confidence ({confidence:.2f}) or low class margin ({margin:.2f})."
                )

        return response
        
# Initialize global service instance
prediction_service_instance = PredictionService()
