"""
Step 8: Validation Check
- Ensures no nulls are present in the final ML-ready datasets.
- Verifies the feature set has exactly 19 features (excluding target).
- Prints a clean sample.
"""

import pandas as pd
from pathlib import Path
import joblib

def main():
    base_dir = Path(__file__).resolve().parent.parent
    processed_dir = base_dir / "data" / "processed"
    models_dir = base_dir / "models"
    
    train_path = processed_dir / "compatibility_train.csv"
    test_path = processed_dir / "compatibility_test.csv"
    features_path = models_dir / "compatibility_features.pkl"
    
    print("=" * 50)
    print("    STEP 8: VALIDATION CHECK")
    print("=" * 50)
    
    print(f"\n[Loading Datasets]")
    try:
        train_df = pd.read_csv(train_path)
        test_df = pd.read_csv(test_path)
        expected_features = joblib.load(features_path)
    except FileNotFoundError as e:
        print(f"Error loading final files: {e}")
        return
        
    print(f"Train Shape: {train_df.shape}")
    print(f"Test Shape:  {test_df.shape}")

    target_col = 'compatibility_score' if 'compatibility_score' in train_df.columns else 'compatible'
    
    # 1. Check Nulls
    train_nulls = train_df.isnull().sum().sum()
    test_nulls = test_df.isnull().sum().sum()
    
    print("\n[Null Values Check]")
    if train_nulls == 0 and test_nulls == 0:
        print("PASS: No null values in final datasets.")
    else:
        print(f"FAIL: Found {train_nulls} nulls in Train, {test_nulls} nulls in Test.")
        
    # 2. Check Features
    print("\n[Feature Set Check]")
    actual_features = [col for col in train_df.columns if col != target_col]
    
    if len(actual_features) == 18:
        print("PASS: Dataset contains exactly 18 features.")
    else:
        print(f"WARNING: Dataset contains {len(actual_features)} features (expected 18).")
        
    if set(actual_features) == set(expected_features):
        print("PASS: Features match the saved compatibility_features.pkl.")
    else:
        print("FAIL: Feature mismatch between dataset and saved standard.")
        
    # 3. Print Clean Sample
    print("\n[Sample Rows - Train Data (Features Only)]")
    # .to_string() avoids aggressive truncation
    print(train_df.drop(columns=[target_col]).head(3).to_string())
    
    print("\n[Sample Distribution Check]")
    print(f"Train '{target_col}':\n{train_df[target_col].value_counts().to_dict()}")
    print(f"Test  '{target_col}':\n{test_df[target_col].value_counts().to_dict()}")
    
    print("\nValidation complete! Data is ready for Model Training.")

if __name__ == "__main__":
    main()
