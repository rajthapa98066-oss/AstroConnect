"""
Step 3: Drop unnecessary columns
- Drops 'person1_name', 'person2_name', and 'total_score'
- Saves the resulting DataFrame to an intermediate CSV in data/processed/
"""

import pandas as pd
from pathlib import Path

def main():
    # 1. Define paths dynamically
    base_dir = Path(__file__).resolve().parent.parent
    raw_data_path = base_dir / "data" / "raw" / "compatibility_pairs.csv"
    processed_dir = base_dir / "data" / "processed"
    temp_step3_path = processed_dir / "temp_step3.csv"
    
    # Ensure processed directory exists
    processed_dir.mkdir(parents=True, exist_ok=True)
    
    print("=" * 50)
    print("    STEP 3: DROP UNNECESSARY COLUMNS")
    print("=" * 50)
    
    # 2. Load the dataset
    print(f"\n[Loading Data] from {raw_data_path}")
    try:
        df = pd.read_csv(raw_data_path)
    except FileNotFoundError:
        print(f"Error: Could not find {raw_data_path}")
        return
        
    print(f"Original dataset shape: {df.shape}")
    
    # 3. Drop columns
    columns_to_drop = [
        'person1_name', 'person2_name', 'total_score',
        'person1_dob', 'person1_tob', 'person1_pob',
        'person2_dob', 'person2_tob', 'person2_pob'
    ]
    
    # We use errors='ignore' in case they were already dropped or not present
    df = df.drop(columns=columns_to_drop, errors='ignore')
    
    print(f"\n[Dropped Columns]: {columns_to_drop}")
    print(f"New dataset shape: {df.shape}")
    
    print("\n[Remaining Columns]")
    print(list(df.columns))
    
    # 4. Save to intermediate file
    print(f"\n[Saving Intermediate Data] to {temp_step3_path}")
    df.to_csv(temp_step3_path, index=False)
    print("Done.")

if __name__ == "__main__":
    main()
