import os
import joblib
import pandas as pd
from catboost import CatBoostClassifier
from sklearn.metrics import accuracy_score, classification_report, confusion_matrix
import matplotlib.pyplot as plt
import seaborn as sns

def main():
    print("==================================================")
    print("    COMPATIBILITY MODEL TRAINING (CATBOOST)")
    print("==================================================\n")

    base_dir = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
    train_path = os.path.join(base_dir, 'data', 'processed', 'compatibility_train.csv')
    test_path = os.path.join(base_dir, 'data', 'processed', 'compatibility_test.csv')

    # 1. Load Data
    print(f"[Loading Datasets]")
    train_df = pd.read_csv(train_path)
    test_df = pd.read_csv(test_path)
    
    X_train = train_df.drop(columns=['compatible'])
    y_train = train_df['compatible']
    
    X_test = test_df.drop(columns=['compatible'])
    y_test = test_df['compatible']
    
    print(f"Train set: {X_train.shape[0]} samples")
    print(f"Test set:  {X_test.shape[0]} samples\n")
    
    # 2. Train CatBoost
    print("[Training CatBoost Classifier]")
    rf_model = CatBoostClassifier(
        loss_function='Logloss',
        auto_class_weights='Balanced',
        iterations=400,
        depth=6,
        learning_rate=0.06,
        random_seed=42,
        allow_writing_files=False,
        verbose=False
    )
    rf_model.fit(X_train, y_train)
    
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
    print(classification_report(y_test_list, y_pred))
    
    # Plot Confusion Matrix
    cm = confusion_matrix(y_test_list, y_pred)
    plt.figure(figsize=(6, 5))
    sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', 
                xticklabels=['Incompatible', 'Compatible'], 
                yticklabels=['Incompatible', 'Compatible'])
    plt.title('Compatibility Model - Confusion Matrix')
    plt.ylabel('Actual')
    plt.xlabel('Predicted')
    
    cm_path = os.path.join(base_dir, 'models', 'compatibility_confusion_matrix.png')
    plt.savefig(cm_path)
    print(f"[Saved Confusion Matrix Visualization] -> {cm_path}")
    
    # Feature Importance
    importances = rf_model.get_feature_importance()
    features = X_train.columns
    feat_df = pd.DataFrame({'Feature': features, 'Importance': importances}).sort_values(by='Importance', ascending=False)
    
    print("\n[Top 5 Most Important Features]")
    for idx, row in feat_df.head(5).iterrows():
        print(f" - {row['Feature']}: {row['Importance']:.4f}")
    
    # 4. Save Model
    model_path = os.path.join(base_dir, 'models', 'compatibility_model.pkl')
    joblib.dump(rf_model, model_path)
    print(f"\n[Saved Model] Successful -> {model_path}")

if __name__ == "__main__":
    main()
