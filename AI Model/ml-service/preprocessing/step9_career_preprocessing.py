import os
import joblib
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
from imblearn.over_sampling import RandomOverSampler
import matplotlib.pyplot as plt
import seaborn as sns

ZODIAC_SIGNS = {
    'Aries', 'Taurus', 'Gemini', 'Cancer', 'Leo', 'Virgo',
    'Libra', 'Scorpio', 'Sagittarius', 'Capricorn', 'Aquarius', 'Pisces'
}


def normalize_sign(value):
    """Normalize sign text and discard non-zodiac values."""
    normalized = str(value).strip().title()
    return normalized if normalized in ZODIAC_SIGNS else None

def main():
    print("==================================================")
    print("    STEP 9: CAREER MODEL PREPROCESSING")
    print("==================================================\n")

    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    input_path = os.path.join(base_dir, 'data', 'raw', 'celebrities_with_planets.csv')
    
    if not os.path.exists(input_path):
        print(f"Error: Could not find raw career dataset at {input_path}")
        return

    print(f"[Loading Data] from {input_path}")
    df = pd.read_csv(input_path)
    
    # 1. Feature Extraction
    print("[Extracting Features]")
    # We want only the Signs or Houses of the Planets.
    # Exclude Names, URLs, Coordinates, Degrees for the baseline model to prevent overfitting on noise.
    feature_cols = [
        'sun_sign', 'moon_sign', 'mars_sign', 'mercury_sign', 
        'jupiter_sign', 'venus_sign', 'saturn_sign', 
        'rahu_sign', 'ketu_sign'
    ]
    target_col = 'career_category'
    
    # Check if necessary columns exist
    missing = [c for c in feature_cols + [target_col] if c not in df.columns]
    if missing:
        print(f"Error: Missing columns in dataset: {missing}")
        return

    # Filter out rows missing the label
    df = df.dropna(subset=[target_col])
    df[target_col] = df[target_col].astype(str).str.strip().replace('', pd.NA)
    df = df.dropna(subset=[target_col])
    
    # Filter dataset down to just features + target
    df = df[feature_cols + [target_col]].copy()

    # Normalize sign text and impute missing/invalid signs with per-column mode.
    for col in feature_cols:
        df[col] = df[col].apply(normalize_sign)
        if df[col].isna().any():
            if df[col].notna().any():
                mode_val = df[col].mode().iloc[0]
            else:
                mode_val = 'Aries'
            df[col] = df[col].fillna(mode_val)

    print(f"Data shape after cleaning: {df.shape}")

    if df.shape[0] < 50:
        print("\nWARNING: Dataset has extremely few samples. Proceeding, but model performance will be poor.")

    # 2. Categorical Encoding
    print("\n[Encoding Features]")
    encoders_dir = os.path.join(base_dir, 'models', 'encoders')
    os.makedirs(encoders_dir, exist_ok=True)
    
    for col in feature_cols:
        le = LabelEncoder()
        df[col] = le.fit_transform(df[col].astype(str))
        joblib.dump(le, os.path.join(encoders_dir, f'career_{col}_encoder.pkl'))

    # Save canonical feature order for inference consistency.
    joblib.dump(feature_cols, os.path.join(base_dir, 'models', 'career_features.pkl'))

    # Encode the Target Variable
    target_le = LabelEncoder()
    df[target_col] = target_le.fit_transform(df[target_col])
    joblib.dump(target_le, os.path.join(encoders_dir, f'career_target_encoder.pkl'))
    
    print(f"[Encoded Classes] {list(target_le.classes_)}")

    # 3. Train/Test Split
    print("\n[Splitting Data] (80% Train, 20% Test, Stratified if possible)")
    X = df.drop(columns=[target_col])
    y = df[target_col]
    
    # Handle the edge case where some classes have only 1 sample (prevents stratified split from working)
    class_counts = y.value_counts()
    valid_classes = class_counts[class_counts > 1].index
    
    # Filter out single-sample classes to allow stratification
    if len(valid_classes) != len(class_counts):
        print("WARNING: Some classes have only 1 sample and will be dropped to allow stratified splitting.")
        valid_idx = y.isin(valid_classes)
        X = X[valid_idx]
        y = y[valid_idx]

    try:
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42, stratify=y
        )
    except ValueError as e:
        # Fallback if stratify still fails due to weird class imbalances
        print("Stratified split failed! Falling back to random split.")
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42
        )

    print(f"Initial Train shape: {X_train.shape}, Test shape: {X_test.shape}")

    # 4. Handle class imbalance with RandomOverSampler.
    # RandomOverSampler is safer for encoded categorical features than vanilla SMOTE.
    print("\n[Applying RandomOverSampler to Train Set]")
    
    # Make sure we have more than 1 class
    if len(y_train.unique()) > 1:
        ros = RandomOverSampler(sampling_strategy='not majority', random_state=42)
        X_train_os, y_train_os = ros.fit_resample(X_train, y_train)

        print(f"New Train shape after oversampling: {X_train_os.shape}")

        # Use resampled data
        train_df = X_train_os.copy()
        train_df[target_col] = y_train_os
    else:
        print("WARNING: Only 1 class present in training data! SMOTE cannot be applied.")
        train_df = X_train.copy()
        train_df[target_col] = y_train

    # Test Data reassembly
    test_df = X_test.copy()
    test_df[target_col] = y_test

    # 5. Save Data
    processed_dir = os.path.join(base_dir, 'data', 'processed')
    os.makedirs(processed_dir, exist_ok=True)
    
    train_path = os.path.join(processed_dir, 'career_train.csv')
    test_path = os.path.join(processed_dir, 'career_test.csv')
    
    train_df.to_csv(train_path, index=False)
    test_df.to_csv(test_path, index=False)
    
    print(f"\n[Saving Train Data] to {train_path}")
    print(f"[Saving Test Data] to {test_path}")
    print("Done. Ready for Career Model Training.")

if __name__ == "__main__":
    main()
