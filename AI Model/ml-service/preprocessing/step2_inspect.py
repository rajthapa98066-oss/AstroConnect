"""
Step 2: Load and inspect data
Loads the compatibility pairs dataset and displays its shape, columns, 
data types, null counts, class distribution, and unique values for key categorical columns.
"""

import pandas as pd
from pathlib import Path

def main():
    # 1. Define paths dynamically based on the script location
    base_dir = Path(__file__).resolve().parent.parent
    data_path = base_dir / "data" / "raw" / "compatibility_pairs.csv"
    
    print("=" * 50)
    print("    STEP 2: DATA INSPECTION")
    print("=" * 50)
    
    # Load compatibility_pairs.csv
    print(f"\n[Loading Dataset] from {data_path}")
    try:
        df = pd.read_csv(data_path)
    except FileNotFoundError:
        print(f"Error: Could not find {data_path}")
        return

    # Print: shape
    print("\n[Dataset Shape]")
    print(f"Rows: {df.shape[0]}, Columns: {df.shape[1]}")
    
    # Print: column names
    print("\n[Column Names]")
    # Using list() to ensure all names print in a readable array format
    print(list(df.columns))
    
    # Print: data types
    print("\n[Data Types]")
    print(df.dtypes)
    
    # Print: null counts
    print("\n[Null Counts]")
    null_counts = df.isnull().sum()
    print(null_counts[null_counts > 0] if null_counts.sum() > 0 else "No null values found!")
    
    # Print: class distribution of 'compatible' column
    print("\n[Class Distribution ('compatible')]")
    # value_counts returns the absolute frequency, while normalize=True returns the relative frequency (percentage)
    counts = df['compatible'].value_counts()
    percentages = df['compatible'].value_counts(normalize=True) * 100
    
    # Combine them for clear viewing
    dist_df = pd.DataFrame({'Count': counts, 'Percentage': percentages.round(2).astype(str) + '%'})
    print(dist_df)
    
    # Print: first 5 rows
    print("\n[First 5 Rows]")
    print(df.head())
    
    # Print: unique values of person1_moon_sign and person1_nakshatra
    print("\n[Unique Values: person1_moon_sign]")
    unique_signs = df['person1_moon_sign'].unique()
    print(f"Count: {len(unique_signs)}")
    print(unique_signs)
    
    print("\n[Unique Values: person1_nakshatra]")
    unique_nakshatras = df['person1_nakshatra'].unique()
    print(f"Count: {len(unique_nakshatras)}")
    print(unique_nakshatras)

if __name__ == "__main__":
    main()
