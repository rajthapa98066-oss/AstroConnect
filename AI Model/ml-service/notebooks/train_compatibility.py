"""
train_compatibility.py - Advanced Training Script for Compatibility Prediction Model

This script trains, evaluates, and saves a Compatibility Prediction Model.
Key features:
- Uses XGBoost for high-performance binary classification.
- Implements hyperparameter optimization with Optuna to find the best model settings.
- Handles class imbalance using XGBoost's `scale_pos_weight` parameter.
- Performs detailed evaluation, including F1-score, classification reports, and confusion matrices.
- Saves the trained model, feature order, and evaluation artifacts for inference.
"""

import os
import joblib
import json
import pandas as pd
import numpy as np
import xgboost as xgb
import optuna
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix, f1_score
import matplotlib.pyplot as plt
import seaborn as sns

# --- Configuration ---
BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
DATA_DIR = os.path.join(BASE_DIR, 'data', 'processed')
MODELS_DIR = os.path.join(BASE_DIR, 'models')
TARGET_COL = 'compatibility_score'
OPTUNA_TRIALS = 75  # Increased trials for potentially more complex interactions
OPTUNA_TIMEOUT = 900 # Increased timeout

def load_data():
    """Loads the processed training and testing datasets for compatibility."""
    print("[1/6] Loading processed compatibility datasets...")
    train_path = os.path.join(DATA_DIR, 'compatibility_train.csv')
    test_path = os.path.join(DATA_DIR, 'compatibility_test.csv')

    if not os.path.exists(train_path) or not os.path.exists(test_path):
        print("❌ Error: Processed compatibility datasets not found.")
        print("Please ensure the data preprocessing steps have been run.")
        return None, None, None, None

    train_df = pd.read_csv(train_path)
    test_df = pd.read_csv(test_path)

    target_col = TARGET_COL if TARGET_COL in train_df.columns else 'compatible'

    X_train = train_df.drop(columns=[target_col])
    y_train = train_df[target_col]
    X_test = test_df.drop(columns=[target_col])
    y_test = test_df[target_col]

    print(f"✅ Train set: {X_train.shape[0]} samples")
    print(f"✅ Test set:  {X_test.shape[0]} samples\n")
    return X_train, y_train, X_test, y_test

def run_hyperparameter_optimization(X_train, y_train, X_val, y_val):
    """
    Uses Optuna to find the best hyperparameters for the XGBoost binary classifier.
    The objective is to maximize the F1-score, which is a good metric for imbalanced binary classification.
    """
    print("[2/6] Running hyperparameter optimization with Optuna...")

    # Calculate scale_pos_weight for handling class imbalance
    # scale_pos_weight = count(negative examples) / count(positive examples)
    negative_count = float(np.sum(y_train == 0))
    positive_count = float(np.sum(y_train == 1))
    scale_pos_weight = negative_count / max(positive_count, 1.0)
    print(f"Calculated scale_pos_weight for imbalance: {scale_pos_weight:.2f}")

    dtrain = xgb.DMatrix(X_train, label=y_train)
    dval = xgb.DMatrix(X_val, label=y_val)

    def objective(trial):
        param = {
            'objective': 'binary:logistic',
            'eval_metric': 'logloss',
            'booster': 'gbtree',
            'scale_pos_weight': scale_pos_weight,
            'lambda': trial.suggest_float('lambda', 1e-8, 1.0, log=True),
            'alpha': trial.suggest_float('alpha', 1e-8, 1.0, log=True),
            'max_depth': trial.suggest_int('max_depth', 3, 10),
            'eta': trial.suggest_float('eta', 0.01, 0.3, log=True),
            'gamma': trial.suggest_float('gamma', 1e-8, 1.0, log=True),
            'colsample_bytree': trial.suggest_float('colsample_bytree', 0.4, 1.0),
            'min_child_weight': trial.suggest_int('min_child_weight', 1, 12),
            'subsample': trial.suggest_float('subsample', 0.5, 1.0),
            'seed': 42
        }

        bst = xgb.train(param, dtrain, evals=[(dval, 'eval')], early_stopping_rounds=50, verbose_eval=False)
        preds_proba = bst.predict(dval)
        preds_binary = (preds_proba > 0.5).astype(int)
        f1 = f1_score(y_val, preds_binary)
        return float(f1)

    study = optuna.create_study(direction='maximize')
    study.optimize(objective, n_trials=OPTUNA_TRIALS, timeout=OPTUNA_TIMEOUT)

    print(f"✅ Optimization finished. Best trial F1-score: {study.best_value:.4f}")
    print(f"✅ Best params: {study.best_params}\n")
    return study.best_params

def train_final_model(X_train, y_train, best_params):
    """Trains the final XGBoost model using the best hyperparameters found by Optuna."""
    print("[3/6] Training final model with best parameters...")
    
    negative_count = float(np.sum(y_train == 0))
    positive_count = float(np.sum(y_train == 1))
    scale_pos_weight = negative_count / max(positive_count, 1.0)
    dtrain = xgb.DMatrix(X_train, label=y_train)

    final_params = {
        'objective': 'binary:logistic',
        'eval_metric': 'logloss',
        'scale_pos_weight': scale_pos_weight,
        'seed': 42,
        **best_params
    }

    model = xgb.train(
        final_params,
        dtrain,
        num_boost_round=600, # Increased rounds for final model
        verbose_eval=False
    )
    print("✅ Final model trained.\n")
    return model

def evaluate_model(model, X_test, y_test):
    """Evaluates the model on the test set and saves a classification report and confusion matrix."""
    print("[4/6] Evaluating model on test data...")
    dtest = xgb.DMatrix(X_test)
    y_pred_proba = model.predict(dtest)
    y_pred = (y_pred_proba > 0.5).astype(int)

    acc = accuracy_score(y_test, y_pred)
    f1 = f1_score(y_test, y_pred)
    print(f"📊 Accuracy: {acc:.4f}")
    print(f"📊 F1-Score: {f1:.4f}")

    # --- Classification Report ---
    class_names = ['Not Compatible', 'Compatible']
    report = classification_report(y_test, y_pred, target_names=class_names, output_dict=True)
    print("\nClassification Report:")
    print(classification_report(y_test, y_pred, target_names=class_names))

    report_path = os.path.join(MODELS_DIR, 'compatibility_classification_report.json')
    with open(report_path, 'w') as f:
        json.dump(report, f, indent=4)
    print(f"✅ Classification report saved to {report_path}")

    # --- Confusion Matrix ---
    cm = confusion_matrix(y_test, y_pred)
    plt.figure(figsize=(8, 6))
    sns.heatmap(cm, annot=True, fmt='d', cmap='Greens', 
                xticklabels=class_names, 
                yticklabels=class_names)
    plt.title('Compatibility Model - Confusion Matrix', fontsize=16)
    plt.ylabel('Actual', fontsize=12)
    plt.xlabel('Predicted', fontsize=12)
    plt.tight_layout()
    
    cm_path = os.path.join(MODELS_DIR, 'compatibility_confusion_matrix.png')
    plt.savefig(cm_path)
    print(f"✅ Confusion matrix saved to {cm_path}\n")

    return model.get_fscore()

def save_artifacts(model, feature_importance, best_params, X_train):
    """Saves the trained model and other essential artifacts for inference."""
    print("[5/6] Saving model and artifacts...")

    # --- Save Model ---
    model_path = os.path.join(MODELS_DIR, 'compatibility_model.pkl')
    joblib.dump(model, model_path)
    print(f"✅ Model saved to {model_path}")

    # --- Save Model Bundle ---
    bundle = {
        "model": model,
        "feature_order": list(X_train.columns),
        "best_params": best_params,
        "feature_importance": feature_importance
    }
    bundle_path = os.path.join(MODELS_DIR, 'compatibility_model_bundle.pkl')
    joblib.dump(bundle, bundle_path)
    print(f"✅ Model bundle saved to {bundle_path}\n")

def show_summary(feature_importance):
    """Prints a summary of the most important features."""
    print("[6/6] Feature Importance Summary")
    if not feature_importance:
        print("No feature importance data available.")
        return
        
    sorted_features = sorted(feature_importance.items(), key=lambda x: x[1], reverse=True)
    print("Top 10 most important features:")
    for feature, score in sorted_features[:10]:
        print(f"  - {feature}: {score}")
    print("\n✨ Training process completed successfully! ✨")

def main():
    """Main function to run the complete training pipeline."""
    print("==========================================================")
    print("    ADVANCED COMPATIBILITY MODEL TRAINING (XGBOOST)")
    print("==========================================================\n")

    X_train, y_train, X_test, y_test = load_data()
    if X_train is None:
        return

    # Use a subset of training data for validation during optimization
    X_train_part, X_val, y_train_part, y_val = train_test_split(X_train, y_train, test_size=0.25, random_state=42, stratify=y_train)

    best_params = run_hyperparameter_optimization(X_train_part, y_train_part, X_val, y_val)
    
    # Train the final model on the full training data
    final_model = train_final_model(X_train, y_train, best_params)
    
    feature_importance = evaluate_model(final_model, X_test, y_test)
    
    save_artifacts(final_model, feature_importance, best_params, X_train)
    
    show_summary(feature_importance)

if __name__ == "__main__":
    main()
