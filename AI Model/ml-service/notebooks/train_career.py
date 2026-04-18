import os
import joblib
import pandas as pd
from catboost import CatBoostClassifier
from sklearn.model_selection import GridSearchCV, StratifiedKFold
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix
import matplotlib.pyplot as plt
import seaborn as sns

def main():
    print("==================================================")
    print("    CAREER MODEL TRAINING (CATBOOST)")
    print("==================================================\n")

    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    train_path = os.path.join(base_dir, 'data', 'processed', 'career_train.csv')
    test_path = os.path.join(base_dir, 'data', 'processed', 'career_test.csv')

    if not os.path.exists(train_path) or not os.path.exists(test_path):
        print(f"Error: Could not find processed career datasets at {train_path} or {test_path}")
        print("Please run preprocessing/step9_career_preprocessing.py first.")
        return

    # 1. Load Data
    print(f"[Loading Datasets]")
    train_df = pd.read_csv(train_path)
    test_df = pd.read_csv(test_path)
    
    # Load Label Encoder for categories so we can map integers back to actual Career Names
    encoders_dir = os.path.join(base_dir, 'models', 'encoders')
    target_encoder_path = os.path.join(encoders_dir, 'career_target_encoder.pkl')
    
    class_names = None
    if os.path.exists(target_encoder_path):
        target_encoder = joblib.load(target_encoder_path)
        class_names = target_encoder.classes_
    
    target_col = 'career_category'
    
    X_train = train_df.drop(columns=[target_col])
    y_train = train_df[target_col]
    
    X_test = test_df.drop(columns=[target_col])
    y_test = test_df[target_col]
    
    print(f"Train set: {X_train.shape[0]} samples")
    print(f"Test set:  {X_test.shape[0]} samples\n")
    
    # 2. Train CatBoost with macro-F1 oriented tuning for imbalanced multi-class setup
    print("[Training & Tuning CatBoost Classifier]")
    base_cb = CatBoostClassifier(
        loss_function='MultiClass',
        auto_class_weights='Balanced',
        random_seed=42,
        allow_writing_files=False,
        verbose=False
    )

    param_grid = {
        'iterations': [250, 500],
        'depth': [4, 6, 8],
        'learning_rate': [0.03, 0.06, 0.1],
        'l2_leaf_reg': [1, 3, 5]
    }

    cv = StratifiedKFold(n_splits=3, shuffle=True, random_state=42)
    grid = GridSearchCV(
        estimator=base_cb,
        param_grid=param_grid,
        scoring='f1_macro',
        cv=cv,
        n_jobs=-1,
        verbose=0
    )
    grid.fit(X_train, y_train)

    rf_model = grid.best_estimator_
    print(f"Best params: {grid.best_params_}")
    print(f"Best CV macro-F1: {grid.best_score_:.4f}")
    
    # 3. Evaluate Model
    print("\n[Evaluating Model on Test Data]")
    y_pred = rf_model.predict(X_test)
    if hasattr(y_pred, "ravel"):
        y_pred = y_pred.ravel()
    y_pred = [int(value) for value in y_pred]
    y_test_list = [int(value) for value in y_test.tolist()]
    
    acc = accuracy_score(y_test_list, y_pred)
    print(f"Accuracy: {acc:.4f}\n")
    print("Classification Report:")
    
    if class_names is not None:
        # Prevent errors if there are classes in classes_ that don't appear in y_test
        labels_present = sorted(list(set(y_test_list) | set(y_pred)))
        target_names_present = [class_names[i] for i in labels_present]
        print(classification_report(y_test_list, y_pred, labels=labels_present, target_names=target_names_present))
    else:
        print(classification_report(y_test_list, y_pred))
    
    # Plot Confusion Matrix
    if class_names is not None:
        cm = confusion_matrix(y_test_list, y_pred, labels=labels_present)
        tick_labels = target_names_present
    else:
        cm = confusion_matrix(y_test_list, y_pred)
        tick_labels = 'auto'
        
    plt.figure(figsize=(10, 8))
    sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', 
                xticklabels=tick_labels, 
                yticklabels=tick_labels)
    plt.title('Career Model - Confusion Matrix')
    plt.ylabel('Actual')
    plt.xlabel('Predicted')
    plt.xticks(rotation=45)
    plt.yticks(rotation=0)
    plt.tight_layout()
    
    cm_path = os.path.join(base_dir, 'models', 'career_confusion_matrix.png')
    plt.savefig(cm_path)
    print(f"[Saved Confusion Matrix Visualization] -> {cm_path}")
    
    # Feature Importance
    importances = rf_model.get_feature_importance()
    features = X_train.columns
    feat_df = pd.DataFrame({'Feature': features, 'Importance': importances}).sort_values(by='Importance', ascending=False)
    
    print("\n[Top 5 Most Important Planetary Sign Features]")
    for idx, row in feat_df.head(5).iterrows():
        print(f" - {row['Feature']}: {row['Importance']:.4f}")
    
    # 4. Save Model
    model_path = os.path.join(base_dir, 'models', 'career_model.pkl')
    joblib.dump(rf_model, model_path)
    print(f"\n[Saved Model] Successful -> {model_path}")

    # Save a model bundle with metadata to keep decoding consistent in serving.
    bundle_path = os.path.join(base_dir, 'models', 'career_model_bundle.pkl')
    bundle = {
        "model": rf_model,
        "feature_order": list(X_train.columns),
        "model_classes": list(rf_model.classes_),
        "target_classes": list(class_names) if class_names is not None else None,
        "best_params": grid.best_params_,
        "best_cv_macro_f1": float(grid.best_score_),
    }
    joblib.dump(bundle, bundle_path)
    print(f"[Saved Model Bundle] Successful -> {bundle_path}")

if __name__ == "__main__":
    main()
