"""
Step 6 & 7: Class Imbalance and Train/Test Split
- Performs stratified Train-Test split.
- Applies SMOTE only to the Train dataset to prevent data leakage.
- Plots pre- and post-SMOTE distributions.
- Saves final compatibility_train.csv and compatibility_test.csv.
"""

import pandas as pd
from sklearn.model_selection import train_test_split
from imblearn.over_sampling import SMOTE
import matplotlib.pyplot as plt
import seaborn as sns
from pathlib import Path

def main():
    base_dir = Path(__file__).resolve().parent.parent
    processed_dir = base_dir / "data" / "processed"
    models_dir = base_dir / "models"
    
    input_path = processed_dir / "temp_step5.csv"
    train_path = processed_dir / "compatibility_train.csv"
    test_path = processed_dir / "compatibility_test.csv"
    
    # Explicitly mapping seaborn palettes without relying on potential deprecations or bad kwargs
    # We will use simple colors to ensure robust plotting.
    
    print("=" * 50)
    print("    STEP 6 & 7: SPLIT & CALSS IMBALANCE (SMOTE)")
    print("=" * 50)
    
    # 1. Load Data
    print(f"\n[Loading Data] from {input_path}")
    try:
        df = pd.read_csv(input_path)
    except FileNotFoundError:
        print(f"Error: Could not find {input_path}")
        return
        
    X = df.drop(columns=['compatible'])
    y = df['compatible']
    
    # 2. Train-Test Split (80/20 Stratified)
    print("\n[Splitting Data] (80% Train, 20% Test, Stratified)")
    X_train, X_test, y_train, y_test = train_test_split(
        X, y, test_size=0.2, random_state=42, stratify=y
    )
    
    print(f"Initial Train shape: {X_train.shape}, Test shape: {X_test.shape}")
    
    # 3. Check Imbalance & Apply SMOTE
    print("\n[Pristine Train Class Distribution]")
    train_dist = y_train.value_counts(normalize=True).round(4) * 100
    print(train_dist)
    
    # We plot the before and after
    fig, axes = plt.subplots(1, 2, figsize=(12, 5))
    
    sns.countplot(x=y_train, ax=axes[0])
    axes[0].set_title(f'Train Target Before SMOTE\n(0: {sum(y_train==0)}, 1: {sum(y_train==1)})')
    
    print("\n[Applying SMOTE to Train Set]")
    smote = SMOTE(random_state=42)
    X_train_resampled, y_train_resampled = smote.fit_resample(X_train, y_train)
    
    print(f"New Train shape after SMOTE: {X_train_resampled.shape}")
    print("\n[Resampled Train Class Distribution]")
    print(pd.Series(y_train_resampled).value_counts(normalize=True).round(4) * 100)
    
    sns.countplot(x=y_train_resampled, ax=axes[1])
    axes[1].set_title(f'Train Target After SMOTE\n(0: {sum(y_train_resampled==0)}, 1: {sum(y_train_resampled==1)})')
    
    plot_path = models_dir / "class_distribution_smote.png"
    plt.tight_layout()
    plt.savefig(plot_path)
    print(f"\n[Saved Distribution Plot] to {plot_path}")
    
    # 4. Save Final CSVs
    # Recombine X and y
    train_final = X_train_resampled.copy()
    train_final['compatible'] = y_train_resampled
    
    test_final = X_test.copy()
    test_final['compatible'] = y_test
    
    print(f"\n[Saving Train Data] to {train_path}")
    train_final.to_csv(train_path, index=False)
    
    print(f"[Saving Test Data] to {test_path}")
    test_final.to_csv(test_path, index=False)
    
    # Clean up intermediate step files (temp_step3, temp_step4, temp_step5)
    print("\n[Cleanup] Removing intermediate temp files...")
    for f in processed_dir.glob("temp_step*.csv"):
        f.unlink()
        
    print("Done.")

if __name__ == "__main__":
    main()
