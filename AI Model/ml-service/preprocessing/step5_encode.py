"""
Step 5: Encode Categorical Features
- Applies consistent encoding to string variables (Moon Signs, Nakshatras).
- Saves the LabelEncoders and feature list for the prediction API.
"""

import pandas as pd
from sklearn.preprocessing import LabelEncoder
import joblib
from pathlib import Path

# --- VEDIC ASTROLOGY MAPS ---
ZODIAC_ORDER = [
    'Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo',
    'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces'
]

NAKSHATRA_ORDER = [
    "Ashwini", "Bharani", "Krittika", "Rohini", "Mrigashira", "Ardra",
    "Punarvasu", "Pushya", "Ashlesha", "Magha", "Purva Phalguni",
    "Uttara Phalguni", "Hasta", "Chitra", "Swati", "Vishakha", "Anuradha",
    "Jyeshtha", "Mula", "Purva Ashadha", "Uttara Ashadha", "Shravana",
    "Dhanishta", "Shatabhisha", "Purva Bhadrapada", "Uttara Bhadrapada", "Revati"
]

def main():
    base_dir = Path(__file__).resolve().parent.parent
    processed_dir = base_dir / "data" / "processed"
    models_dir = base_dir / "models"
    input_path = processed_dir / "temp_step4.csv"
    output_path = processed_dir / "temp_step5.csv"
    
    models_dir.mkdir(parents=True, exist_ok=True)
    
    print("=" * 50)
    print("    STEP 5: CATEGORICAL ENCODING")
    print("=" * 50)
    
    print(f"\n[Loading Data] from {input_path}")
    try:
        df = pd.read_csv(input_path)
    except FileNotFoundError:
        print(f"Error: Could not find {input_path}")
        return

    # Initialize Encoders
    moon_sign_encoder = LabelEncoder()
    nakshatra_encoder = LabelEncoder()
    
    # Fit globally to ensure string names get the EXACT same mapping index
    moon_sign_encoder.fit(ZODIAC_ORDER)
    nakshatra_encoder.fit(NAKSHATRA_ORDER)
    
    print("\n[Encoding Features]")
    # Transform
    df['person1_moon_sign'] = moon_sign_encoder.transform(df['person1_moon_sign'])
    df['person2_moon_sign'] = moon_sign_encoder.transform(df['person2_moon_sign'])
    df['person1_nakshatra'] = nakshatra_encoder.transform(df['person1_nakshatra'])
    df['person2_nakshatra'] = nakshatra_encoder.transform(df['person2_nakshatra'])
    
    target_cols = df.select_dtypes(include=['object']).columns
    if len(target_cols) > 0:
        print(f"Warning: Found remaining unencoded object columns: {list(target_cols)}")
        
    print(f"\nData sample after encoding:")
    print(df[['person1_moon_sign', 'person1_nakshatra']].head(3))
    
    # Save encoders
    moon_encoder_path = models_dir / "moon_sign_encoder.pkl"
    nakshatra_encoder_path = models_dir / "nakshatra_encoder.pkl"
    features_path = models_dir / "compatibility_features.pkl"
    
    print(f"\n[Saving Encoders] to {models_dir}")
    joblib.dump(moon_sign_encoder, moon_encoder_path)
    joblib.dump(nakshatra_encoder, nakshatra_encoder_path)
    
    # Save feature names
    features = [col for col in df.columns if col != 'compatible']
    joblib.dump(features, features_path)
    print(f"Saved feature list ({len(features)} features)")
    
    print(f"\n[Saving Data] to {output_path}")
    df.to_csv(output_path, index=False)
    print("Done.")

if __name__ == "__main__":
    main()
