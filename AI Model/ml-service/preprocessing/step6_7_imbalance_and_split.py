"""
Step 6 & 7: Compatibility train/test split.

This script:
- Loads processed compatibility features from temp_step5.csv.
- Uses a stratified 80/20 split.
- Saves compatibility_train.csv and compatibility_test.csv with target column
  compatibility_score.
- Saves compatibility feature order for inference.
"""

from pathlib import Path

import joblib
import pandas as pd
from sklearn.model_selection import train_test_split


def main() -> None:
    base_dir = Path(__file__).resolve().parent.parent
    processed_dir = base_dir / "data" / "processed"
    models_dir = base_dir / "models"

    input_path = processed_dir / "temp_step5.csv"
    train_path = processed_dir / "compatibility_train.csv"
    test_path = processed_dir / "compatibility_test.csv"
    feature_path = models_dir / "compatibility_features.pkl"

    print("=" * 60)
    print("    STEP 6 & 7: COMPATIBILITY SPLIT (STRATIFIED)")
    print("=" * 60)

    if not input_path.exists():
        print(f"Error: Could not find {input_path}")
        return

    df = pd.read_csv(input_path)

    # Support both old and new target naming.
    if "compatibility_score" in df.columns:
        source_target = "compatibility_score"
    elif "compatible" in df.columns:
        source_target = "compatible"
    else:
        print("Error: Could not find target column ('compatibility_score' or 'compatible').")
        return

    X = df.drop(columns=[source_target])
    y = df[source_target].astype(int)

    X_train, X_test, y_train, y_test = train_test_split(
        X,
        y,
        test_size=0.2,
        random_state=42,
        stratify=y,
    )

    train_df = X_train.copy()
    train_df["compatibility_score"] = y_train.values

    test_df = X_test.copy()
    test_df["compatibility_score"] = y_test.values

    train_df.to_csv(train_path, index=False)
    test_df.to_csv(test_path, index=False)
    joblib.dump(list(X.columns), feature_path)

    print(f"Train rows: {len(train_df)}")
    print(f"Test rows: {len(test_df)}")
    print(f"Saved: {train_path}")
    print(f"Saved: {test_path}")
    print(f"Saved: {feature_path}")


if __name__ == "__main__":
    main()
