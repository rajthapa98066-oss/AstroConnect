"""
Step 4: Feature Engineering
Calculates 6 new Vedic astrology-based features:
1. sign_distance
2. sign_element_match
3. sign_quality_match
4. sign_compatibility
5. nakshatra_distance
6. nakshatra_group_match
"""

import pandas as pd
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

ELEMENTS = {
    'Fire': ['Aries', 'Leo', 'Sagittarius'],
    'Earth': ['Taurus', 'Virgo', 'Capricorn'],
    'Air': ['Gemini', 'Libra', 'Aquarius'],
    'Water': ['Cancer', 'Scorpio', 'Pisces']
}
SIGN_ELEMENT_MAP = {sign: elem for elem, signs in ELEMENTS.items() for sign in signs}

QUALITIES = {
    'Cardinal': ['Aries', 'Cancer', 'Libra', 'Capricorn'],
    'Fixed': ['Taurus', 'Leo', 'Scorpio', 'Aquarius'],
    'Mutable': ['Gemini', 'Virgo', 'Sagittarius', 'Pisces']
}
SIGN_QUALITY_MAP = {sign: qual for qual, signs in QUALITIES.items() for sign in signs}

COMPATIBLE_PAIRS = [
    ('Aries', 'Leo'), ('Aries', 'Sagittarius'), ('Taurus', 'Virgo'), ('Taurus', 'Capricorn'),
    ('Gemini', 'Libra'), ('Gemini', 'Aquarius'), ('Cancer', 'Scorpio'), ('Cancer', 'Pisces'),
    ('Leo', 'Sagittarius'), ('Virgo', 'Capricorn'), ('Libra', 'Aquarius'), ('Scorpio', 'Pisces')
]

NAKSHATRA_GANAS = {
    'Deva': ["Ashwini", "Mrigashira", "Punarvasu", "Pushya", "Hasta", "Swati", "Anuradha", "Shravana", "Revati"],
    'Manushya': ["Bharani", "Rohini", "Ardra", "Purva Phalguni", "Uttara Phalguni", "Purva Ashadha", "Uttara Ashadha", "Purva Bhadrapada", "Uttara Bhadrapada"],
    'Rakshasa': ["Krittika", "Ashlesha", "Magha", "Chitra", "Vishakha", "Jyeshtha", "Mula", "Dhanishta", "Shatabhisha"]
}
NAKSHATRA_GANA_MAP = {nak: gana for gana, naks in NAKSHATRA_GANAS.items() for nak in naks}

# --- HELPER FUNCTIONS ---
def get_circular_distance(idx1, idx2, total_elements):
    """Calculates shortest distance in a circular array."""
    raw_dist = abs(idx1 - idx2)
    return min(raw_dist, total_elements - raw_dist)

def is_highly_compatible(s1, s2):
    return int((s1, s2) in COMPATIBLE_PAIRS or (s2, s1) in COMPATIBLE_PAIRS)

def main():
    base_dir = Path(__file__).resolve().parent.parent
    processed_dir = base_dir / "data" / "processed"
    input_path = processed_dir / "temp_step3.csv"
    output_path = processed_dir / "temp_step4.csv"
    
    print("=" * 50)
    print("    STEP 4: FEATURE ENGINEERING")
    print("=" * 50)
    
    print(f"\n[Loading Data] from {input_path}")
    try:
        df = pd.read_csv(input_path)
    except FileNotFoundError:
        print(f"Error: Could not find {input_path}")
        return

    print("Generating new features...")
    
    # 1. sign_distance
    sign_indices_1 = df['person1_moon_sign'].apply(lambda x: ZODIAC_ORDER.index(x) if x in ZODIAC_ORDER else 0)
    sign_indices_2 = df['person2_moon_sign'].apply(lambda x: ZODIAC_ORDER.index(x) if x in ZODIAC_ORDER else 0)
    df['sign_distance'] = [get_circular_distance(i1, i2, 12) for i1, i2 in zip(sign_indices_1, sign_indices_2)]
    
    # 2. sign_element_match
    df['sign_element_match'] = (
        df['person1_moon_sign'].map(SIGN_ELEMENT_MAP) == 
        df['person2_moon_sign'].map(SIGN_ELEMENT_MAP)
    ).astype(int)
    
    # 3. sign_quality_match
    df['sign_quality_match'] = (
        df['person1_moon_sign'].map(SIGN_QUALITY_MAP) == 
        df['person2_moon_sign'].map(SIGN_QUALITY_MAP)
    ).astype(int)
    
    # 4. sign_compatibility
    df['sign_compatibility'] = df.apply(
        lambda row: is_highly_compatible(row['person1_moon_sign'], row['person2_moon_sign']), 
        axis=1
    )
    
    # 5. nakshatra_distance
    nak_indices_1 = df['person1_nakshatra'].apply(lambda x: NAKSHATRA_ORDER.index(x) if x in NAKSHATRA_ORDER else 0)
    nak_indices_2 = df['person2_nakshatra'].apply(lambda x: NAKSHATRA_ORDER.index(x) if x in NAKSHATRA_ORDER else 0)
    df['nakshatra_distance'] = [get_circular_distance(i1, i2, 27) for i1, i2 in zip(nak_indices_1, nak_indices_2)]
    
    # 6. nakshatra_group_match (Gana match)
    df['nakshatra_group_match'] = (
        df['person1_nakshatra'].map(NAKSHATRA_GANA_MAP) == 
        df['person2_nakshatra'].map(NAKSHATRA_GANA_MAP)
    ).astype(int)
    
    print(f"\n[Feature Engineering Complete] Final Features: {df.shape[1]}")
    print(list(df.columns))
    
    print(f"\n[Saving Data] to {output_path}")
    df.to_csv(output_path, index=False)
    print("Done.")

if __name__ == "__main__":
    main()
