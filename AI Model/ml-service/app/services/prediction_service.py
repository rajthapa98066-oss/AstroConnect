import joblib
import numpy as np
import pandas as pd
from pathlib import Path

# Need to import constants from preprocessing to avoid code duplication
import sys

BASE_DIR = Path(__file__).resolve().parent.parent.parent
sys.path.insert(0, str(BASE_DIR))

from preprocessing.step4_feature_engineering import (
    ZODIAC_ORDER,
    SIGN_ELEMENT_MAP,
    SIGN_QUALITY_MAP,
    NAKSHATRA_ORDER,
    NAKSHATRA_GANA_MAP,
    get_circular_distance,
    is_highly_compatible,
)

import xgboost as xgb


class PredictionService:
    def __init__(self):
        self.models_dir = BASE_DIR / "models"

        compat_bundle_path = self.models_dir / "compatibility_model_bundle.pkl"
        if compat_bundle_path.exists():
            compat_bundle = joblib.load(compat_bundle_path)
            self.compat_model = compat_bundle["model"]
            self.compat_features = compat_bundle["feature_order"]
        else:
            self.compat_model = joblib.load(self.models_dir / "compatibility_model.pkl")
            self.compat_features = joblib.load(self.models_dir / "compatibility_features.pkl")

        self.moon_sign_encoder = joblib.load(self.models_dir / "moon_sign_encoder.pkl")
        self.nakshatra_encoder = joblib.load(self.models_dir / "nakshatra_encoder.pkl")

    @staticmethod
    def _normalize_sign(sign: str) -> str:
        return str(sign).strip().title()

    @staticmethod
    def _supports_predict_proba(model) -> bool:
        return hasattr(model, "predict_proba")

    def _predict_compatibility_probability(self, ordered_features: list[float]) -> float:
        if self._supports_predict_proba(self.compat_model):
            probs = self.compat_model.predict_proba([ordered_features])[0]
            return float(probs[1])

        feature_df = pd.DataFrame([ordered_features], columns=self.compat_features)
        dmatrix = xgb.DMatrix(feature_df, feature_names=self.compat_features)
        pred = self.compat_model.predict(dmatrix)
        return float(np.asarray(pred).reshape(-1)[0])

    def predict_compatibility(self, data: dict) -> dict:
        sign1 = self._normalize_sign(data["person1_moon_sign"])
        sign2 = self._normalize_sign(data["person2_moon_sign"])
        nak1 = self._normalize_sign(data["person1_nakshatra"])
        nak2 = self._normalize_sign(data["person2_nakshatra"])

        sign_idx_1 = ZODIAC_ORDER.index(sign1)
        sign_idx_2 = ZODIAC_ORDER.index(sign2)
        sign_distance = get_circular_distance(sign_idx_1, sign_idx_2, 12)
        sign_element_match = 1 if SIGN_ELEMENT_MAP[sign1] == SIGN_ELEMENT_MAP[sign2] else 0
        sign_quality_match = 1 if SIGN_QUALITY_MAP[sign1] == SIGN_QUALITY_MAP[sign2] else 0
        sign_compatibility = is_highly_compatible(sign1, sign2)

        nak_idx_1 = NAKSHATRA_ORDER.index(nak1)
        nak_idx_2 = NAKSHATRA_ORDER.index(nak2)
        nakshatra_distance = get_circular_distance(nak_idx_1, nak_idx_2, 27)
        nakshatra_group_match = (
            1 if NAKSHATRA_GANA_MAP[nak1] == NAKSHATRA_GANA_MAP[nak2] else 0
        )

        features = {
            "person1_moon_sign": self.moon_sign_encoder.transform([sign1])[0],
            "person1_nakshatra": self.nakshatra_encoder.transform([nak1])[0],
            "person2_moon_sign": self.moon_sign_encoder.transform([sign2])[0],
            "person2_nakshatra": self.nakshatra_encoder.transform([nak2])[0],
            "varna_score": data["varna_score"],
            "vashya_score": data["vashya_score"],
            "tara_score": data["tara_score"],
            "yoni_score": data["yoni_score"],
            "graha_maitri_score": data["graha_maitri_score"],
            "gana_score": data["gana_score"],
            "bhakoot_score": data["bhakoot_score"],
            "nadi_score": data["nadi_score"],
            "sign_distance": sign_distance,
            "sign_element_match": sign_element_match,
            "sign_quality_match": sign_quality_match,
            "sign_compatibility": sign_compatibility,
            "nakshatra_distance": nakshatra_distance,
            "nakshatra_group_match": nakshatra_group_match,
        }

        ordered_features = []
        for feature_name in self.compat_features:
            if feature_name not in features:
                raise ValueError(f"Missing compatibility feature in input pipeline: {feature_name}")
            ordered_features.append(features[feature_name])

        prob_compatible = self._predict_compatibility_probability(ordered_features)
        prediction = 1 if prob_compatible > 0.5 else 0
        confidence = prob_compatible if prediction == 1 else (1.0 - prob_compatible)

        return {
            "prediction": "Highly Compatible" if prediction == 1 else "Not Compatible",
            "is_compatible": bool(prediction),
            "confidence_score": float(confidence),
        }


prediction_service_instance = PredictionService()
