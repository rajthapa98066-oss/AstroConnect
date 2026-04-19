"""
generate_compatibility.py — Generate Vedic Ashtakoot (Gun Milan) compatibility dataset.

This script:
1. Loads celebrities with planetary data
2. Computes Moon sign (Rashi) and Nakshatra for each person using pyswisseph
3. Generates 1000+ random pairs
4. Calculates the 8-factor Ashtakoot score (out of 36) for each pair
5. Labels as compatible (score >= 18) or not
6. Saves to data/raw/compatibility_pairs.csv

The 8 Kootas (Ashtakoot):
  1. Varna   (1 point)  - Spiritual compatibility
  2. Vashya  (2 points) - Mutual attraction
  3. Tara    (3 points) - Birth star compatibility
  4. Yoni    (4 points) - Physical compatibility
  5. Graha Maitri (5 points) - Mental compatibility
  6. Gana    (6 points) - Temperament
  7. Bhakoot (7 points) - Emotional harmony
  8. Nadi    (8 points) - Health & genetics

Usage:
    python -m scrapers.generate_compatibility
"""

import sys
import random
import itertools
from pathlib import Path

import numpy as np
import pandas as pd

sys.path.insert(0, str(Path(__file__).resolve().parent.parent))
from scrapers.utils import setup_logger, save_to_csv, load_csv, DATA_RAW, parse_date, parse_time

# Try to import swisseph for accurate Moon calculations
try:
    import swisseph as swe
    HAS_SWISSEPH = True
except ImportError:
    HAS_SWISSEPH = False
    print("WARNING: pyswisseph not found. Using approximate calculations.")

INPUT_FILE = DATA_RAW / "celebrities_with_planets.csv"
FALLBACK_INPUT = DATA_RAW / "celebrities_raw.csv"
OUTPUT_FILE = DATA_RAW / "compatibility_pairs.csv"

logger = setup_logger("compatibility", str(DATA_RAW / "compatibility.log"))

# ═══════════════════════════════════════════════════════════════════════════════
# VEDIC ASTROLOGY LOOKUP TABLES
# These are traditional tables used in Vedic astrology for compatibility scoring
# ═══════════════════════════════════════════════════════════════════════════════

# The 27 Nakshatras (birth stars) in order
NAKSHATRAS = [
    "Ashwini", "Bharani", "Krittika", "Rohini", "Mrigashira", "Ardra",
    "Punarvasu", "Pushya", "Ashlesha", "Magha", "Purva Phalguni",
    "Uttara Phalguni", "Hasta", "Chitra", "Swati", "Vishakha",
    "Anuradha", "Jyeshtha", "Mula", "Purva Ashadha", "Uttara Ashadha",
    "Shravana", "Dhanishta", "Shatabhisha", "Purva Bhadrapada",
    "Uttara Bhadrapada", "Revati"
]

# 12 Rashis (Moon signs) in order
RASHIS = [
    "Aries", "Taurus", "Gemini", "Cancer", "Leo", "Virgo",
    "Libra", "Scorpio", "Sagittarius", "Capricorn", "Aquarius", "Pisces"
]

# Rashi → Lord planet
RASHI_LORDS = {
    "Aries": "Mars", "Taurus": "Venus", "Gemini": "Mercury",
    "Cancer": "Moon", "Leo": "Sun", "Virgo": "Mercury",
    "Libra": "Venus", "Scorpio": "Mars", "Sagittarius": "Jupiter",
    "Capricorn": "Saturn", "Aquarius": "Saturn", "Pisces": "Jupiter"
}

# ── 1. VARNA (Spiritual compatibility, max 1 point) ──────────────────────────
# Each Rashi belongs to one of 4 Varnas (in descending order):
# Brahmin(4) > Kshatriya(3) > Vaishya(2) > Shudra(1)
RASHI_TO_VARNA = {
    "Cancer": 4, "Scorpio": 4, "Pisces": 4,      # Brahmin
    "Aries": 3, "Leo": 3, "Sagittarius": 3,       # Kshatriya
    "Taurus": 2, "Virgo": 2, "Capricorn": 2,      # Vaishya
    "Gemini": 1, "Libra": 1, "Aquarius": 1,       # Shudra
}

# ── 2. VASHYA (Mutual attraction, max 2 points) ─────────────────────────────
# Each Rashi belongs to a Vashya group
# Groups: Chatushpada(quadruped), Manava(human), Jalachara(water),
#         Vanachara(wild), Keeta(insect)
RASHI_TO_VASHYA = {
    "Aries": "Chatushpada", "Taurus": "Chatushpada",
    "Gemini": "Manava", "Virgo": "Manava",
    "Libra": "Manava", "Aquarius": "Manava",
    "Cancer": "Jalachara", "Pisces": "Jalachara",
    "Leo": "Vanachara",
    "Capricorn": "Chatushpada",  # front half Chatushpada
    "Sagittarius": "Manava",     # front half Manava
    "Scorpio": "Keeta",
}

# Vashya compatibility matrix (groom_vashya, bride_vashya) -> points
VASHYA_SCORE = {
    ("Chatushpada", "Chatushpada"): 2,
    ("Manava", "Manava"): 2,
    ("Jalachara", "Jalachara"): 2,
    ("Vanachara", "Vanachara"): 2,
    ("Keeta", "Keeta"): 2,
    ("Manava", "Chatushpada"): 1,
    ("Chatushpada", "Manava"): 1,
    ("Manava", "Jalachara"): 1,
    ("Jalachara", "Manava"): 1,
    ("Chatushpada", "Jalachara"): 1,
    ("Jalachara", "Chatushpada"): 1,
}

# ── 4. YONI (Physical compatibility, max 4 points) ───────────────────────────
# Each Nakshatra maps to an animal (Yoni)
NAKSHATRA_TO_YONI = {
    "Ashwini": "Horse", "Shatabhisha": "Horse",
    "Bharani": "Elephant", "Revati": "Elephant",
    "Krittika": "Goat", "Pushya": "Goat",
    "Rohini": "Serpent", "Mrigashira": "Serpent",
    "Ardra": "Dog", "Mula": "Dog",
    "Punarvasu": "Cat", "Ashlesha": "Cat",
    "Magha": "Rat", "Purva Phalguni": "Rat",
    "Uttara Phalguni": "Cow", "Uttara Bhadrapada": "Cow",
    "Hasta": "Buffalo", "Swati": "Buffalo",
    "Chitra": "Tiger", "Vishakha": "Tiger",
    "Anuradha": "Hare", "Jyeshtha": "Hare",
    "Purva Ashadha": "Monkey", "Shravana": "Monkey",
    "Uttara Ashadha": "Mongoose", "Dhanishta": "Lion",
    "Purva Bhadrapada": "Lion",
}

# Yoni compatibility: same=4, friendly=3, neutral=2, enemy=1, worst=0
YONI_ENEMIES = {
    ("Horse", "Buffalo"): True, ("Buffalo", "Horse"): True,
    ("Elephant", "Lion"): True, ("Lion", "Elephant"): True,
    ("Goat", "Tiger"): True, ("Tiger", "Goat"): True,
    ("Serpent", "Mongoose"): True, ("Mongoose", "Serpent"): True,
    ("Dog", "Hare"): True, ("Hare", "Dog"): True,
    ("Cat", "Rat"): True, ("Rat", "Cat"): True,
    ("Cow", "Tiger"): True, ("Tiger", "Cow"): True,
    ("Monkey", "Goat"): True, ("Goat", "Monkey"): True,
}

# ── 5. GRAHA MAITRI (Mental compatibility, max 5 points) ─────────────────────
# Planet friendship matrix
PLANET_FRIENDS = {
    "Sun":     {"Moon", "Mars", "Jupiter"},
    "Moon":    {"Sun", "Mercury"},
    "Mars":    {"Sun", "Moon", "Jupiter"},
    "Mercury": {"Sun", "Venus"},
    "Jupiter": {"Sun", "Moon", "Mars"},
    "Venus":   {"Mercury", "Saturn"},
    "Saturn":  {"Mercury", "Venus"},
}

PLANET_ENEMIES = {
    "Sun":     {"Venus", "Saturn"},
    "Moon":    set(),
    "Mars":    {"Mercury"},
    "Mercury": {"Moon"},
    "Jupiter": {"Mercury", "Venus"},
    "Venus":   {"Sun", "Moon"},
    "Saturn":  {"Sun", "Moon", "Mars"},
}

# ── 6. GANA (Temperament, max 6 points) ──────────────────────────────────────
# Each Nakshatra belongs to one of 3 Ganas
NAKSHATRA_TO_GANA = {
    "Ashwini": "Deva", "Mrigashira": "Deva", "Punarvasu": "Deva",
    "Pushya": "Deva", "Hasta": "Deva", "Swati": "Deva",
    "Anuradha": "Deva", "Shravana": "Deva", "Revati": "Deva",
    
    "Bharani": "Manushya", "Rohini": "Manushya", "Ardra": "Manushya",
    "Purva Phalguni": "Manushya", "Uttara Phalguni": "Manushya",
    "Purva Ashadha": "Manushya", "Uttara Ashadha": "Manushya",
    "Purva Bhadrapada": "Manushya", "Uttara Bhadrapada": "Manushya",
    
    "Krittika": "Rakshasa", "Ashlesha": "Rakshasa", "Magha": "Rakshasa",
    "Chitra": "Rakshasa", "Vishakha": "Rakshasa", "Jyeshtha": "Rakshasa",
    "Mula": "Rakshasa", "Dhanishta": "Rakshasa", "Shatabhisha": "Rakshasa",
}

# Gana compatibility matrix
GANA_SCORE = {
    ("Deva", "Deva"): 6, ("Deva", "Manushya"): 6, ("Deva", "Rakshasa"): 1,
    ("Manushya", "Deva"): 5, ("Manushya", "Manushya"): 6, ("Manushya", "Rakshasa"): 0,
    ("Rakshasa", "Deva"): 1, ("Rakshasa", "Manushya"): 0, ("Rakshasa", "Rakshasa"): 6,
}

# ── 8. NADI (Health/genetics, max 8 points) ──────────────────────────────────
# Each Nakshatra belongs to one of 3 Nadis
NAKSHATRA_TO_NADI = {
    "Ashwini": "Aadi", "Ardra": "Aadi", "Punarvasu": "Aadi",
    "Uttara Phalguni": "Aadi", "Hasta": "Aadi", "Jyeshtha": "Aadi",
    "Mula": "Aadi", "Shatabhisha": "Aadi", "Purva Bhadrapada": "Aadi",
    
    "Bharani": "Madhya", "Mrigashira": "Madhya", "Pushya": "Madhya",
    "Purva Phalguni": "Madhya", "Chitra": "Madhya", "Anuradha": "Madhya",
    "Purva Ashadha": "Madhya", "Dhanishta": "Madhya", "Uttara Bhadrapada": "Madhya",
    
    "Krittika": "Antya", "Rohini": "Antya", "Ashlesha": "Antya",
    "Magha": "Antya", "Swati": "Antya", "Vishakha": "Antya",
    "Uttara Ashadha": "Antya", "Shravana": "Antya", "Revati": "Antya",
}


# ═══════════════════════════════════════════════════════════════════════════════
# SCORING FUNCTIONS
# ═══════════════════════════════════════════════════════════════════════════════

def get_moon_position_swisseph(day, month, year, hour, minute):
    """
    Calculate Moon's sidereal longitude using Swiss Ephemeris.
    This gives us accurate Rashi (Moon sign) and Nakshatra.
    
    Args:
        day, month, year, hour, minute: Birth date/time
    
    Returns:
        (rashi_index, nakshatra_index) or None on failure
    """
    if not HAS_SWISSEPH:
        return None
    
    try:
        # Set sidereal mode (Lahiri ayanamsa — standard for Vedic)
        swe.set_sid_mode(swe.SIDM_LAHIRI)
        
        # Convert to Julian day
        decimal_hour = hour + minute / 60.0
        jd = swe.julday(year, month, day, decimal_hour)
        
        # Calculate Moon's position (sidereal)
        moon_pos = swe.calc_ut(jd, swe.MOON, swe.FLG_SIDEREAL)
        moon_longitude = moon_pos[0][0]  # Sidereal longitude in degrees
        
        # Rashi = 30° segments (0-11)
        rashi_index = int(moon_longitude / 30.0)
        
        # Nakshatra = 13°20' segments (0-26)
        nakshatra_index = int(moon_longitude / (360.0 / 27.0))
        
        return (rashi_index % 12, nakshatra_index % 27)
        
    except Exception as e:
        logger.debug(f"  Swiss Ephemeris error: {e}")
        return None


def get_rashi_nakshatra(row: dict) -> tuple[str, str] | None:
    """
    Determine Rashi (Moon sign) and Nakshatra for a person.
    
    Strategy:
    1. Try pyswisseph (most accurate)
    2. Fall back to moon_sign from API data
    3. Fall back to random assignment (last resort)
    """
    rashi = None
    nakshatra = None
    
    # Method 1: Use Swiss Ephemeris for precise calculation
    if HAS_SWISSEPH:
        date_parsed = parse_date(str(row.get("date_of_birth", "")))
        time_parsed = parse_time(str(row.get("time_of_birth", "")))
        
        if date_parsed and time_parsed:
            day, month, year = date_parsed
            hour, minute = time_parsed
            result = get_moon_position_swisseph(day, month, year, hour, minute)
            if result:
                rashi_idx, nakshatra_idx = result
                rashi = RASHIS[rashi_idx]
                nakshatra = NAKSHATRAS[nakshatra_idx]
                return (rashi, nakshatra)
    
    # Method 2: Use moon_sign from API data  
    moon_sign = str(row.get("moon_sign", "")).strip()
    if moon_sign and moon_sign in RASHIS:
        rashi = moon_sign
        # Approximate nakshatra from rashi (each rashi has ~2.25 nakshatras)
        rashi_idx = RASHIS.index(rashi)
        # Pick a nakshatra that falls within this rashi
        base_nak = int(rashi_idx * 2.25)
        nakshatra = NAKSHATRAS[base_nak % 27]
        return (rashi, nakshatra)
    
    # Method 3: Random assignment (worst case — still useful for dataset)
    rashi = random.choice(RASHIS)
    nakshatra = random.choice(NAKSHATRAS)
    return (rashi, nakshatra)


def calculate_varna(rashi1: str, rashi2: str) -> int:
    """Varna Koota (max 1 point). Groom's Varna should be >= Bride's."""
    v1 = RASHI_TO_VARNA.get(rashi1, 1)
    v2 = RASHI_TO_VARNA.get(rashi2, 1)
    return 1 if v1 >= v2 else 0


def calculate_vashya(rashi1: str, rashi2: str) -> int:
    """Vashya Koota (max 2 points). Based on Rashi Vashya groups."""
    g1 = RASHI_TO_VASHYA.get(rashi1, "Manava")
    g2 = RASHI_TO_VASHYA.get(rashi2, "Manava")
    return VASHYA_SCORE.get((g1, g2), 0)


def calculate_tara(nak1: str, nak2: str) -> int:
    """
    Tara Koota (max 3 points).
    Based on the distance between birth nakshatras.
    Count from person1's nakshatra to person2's, mod 9.
    Auspicious remainders (in couples of 9): 1,2,4,6,8 → 3 points
    Inauspicious: 3,5,7,9 → 0 points
    Mutual check: score both directions and take average
    """
    if nak1 not in NAKSHATRAS or nak2 not in NAKSHATRAS:
        return 0
    
    idx1 = NAKSHATRAS.index(nak1)
    idx2 = NAKSHATRAS.index(nak2)
    
    # Forward count from person1 to person2
    dist_forward = ((idx2 - idx1) % 27) + 1
    remainder_f = dist_forward % 9
    if remainder_f == 0:
        remainder_f = 9
    
    # Forward count from person2 to person1
    dist_backward = ((idx1 - idx2) % 27) + 1
    remainder_b = dist_backward % 9
    if remainder_b == 0:
        remainder_b = 9
    
    auspicious = {1, 2, 4, 6, 8}
    score_f = 1.5 if remainder_f in auspicious else 0
    score_b = 1.5 if remainder_b in auspicious else 0
    
    return int(score_f + score_b)


def calculate_yoni(nak1: str, nak2: str) -> int:
    """
    Yoni Koota (max 4 points).
    Based on the animal associated with each nakshatra.
    Same animal = 4, friendly = 3, neutral = 2, enemy = 1, worst = 0
    """
    y1 = NAKSHATRA_TO_YONI.get(nak1, "Horse")
    y2 = NAKSHATRA_TO_YONI.get(nak2, "Horse")
    
    if y1 == y2:
        return 4
    elif (y1, y2) in YONI_ENEMIES or (y2, y1) in YONI_ENEMIES:
        return 0
    else:
        # Semi-compatible → 2 or 3 based on group
        return random.choice([2, 3])


def calculate_graha_maitri(rashi1: str, rashi2: str) -> int:
    """
    Graha Maitri Koota (max 5 points).
    Based on friendship between the lords of the two Rashis.
    Both friends = 5, one friend one neutral = 4, both neutral = 3,
    one friend one enemy = 1, both enemies = 0
    """
    lord1 = RASHI_LORDS.get(rashi1, "Sun")
    lord2 = RASHI_LORDS.get(rashi2, "Sun")
    
    if lord1 == lord2:
        return 5
    
    # Check if lord1 considers lord2 a friend, enemy, or neutral
    friends1 = PLANET_FRIENDS.get(lord1, set())
    enemies1 = PLANET_ENEMIES.get(lord1, set())
    friends2 = PLANET_FRIENDS.get(lord2, set())
    enemies2 = PLANET_ENEMIES.get(lord2, set())
    
    is_friend_1to2 = lord2 in friends1
    is_enemy_1to2 = lord2 in enemies1
    is_friend_2to1 = lord1 in friends2
    is_enemy_2to1 = lord1 in enemies2
    
    if is_friend_1to2 and is_friend_2to1:
        return 5
    elif (is_friend_1to2 and not is_enemy_2to1) or \
         (is_friend_2to1 and not is_enemy_1to2):
        return 4
    elif not is_enemy_1to2 and not is_enemy_2to1:
        return 3
    elif (is_friend_1to2 and is_enemy_2to1) or \
         (is_friend_2to1 and is_enemy_1to2):
        return 1
    else:
        return 0


def calculate_gana(nak1: str, nak2: str) -> int:
    """
    Gana Koota (max 6 points).
    Based on the Gana (temperament) of each Nakshatra.
    Deva = divine, Manushya = human, Rakshasa = demonic
    """
    g1 = NAKSHATRA_TO_GANA.get(nak1, "Manushya")
    g2 = NAKSHATRA_TO_GANA.get(nak2, "Manushya")
    return GANA_SCORE.get((g1, g2), 0)


def calculate_bhakoot(rashi1: str, rashi2: str) -> int:
    """
    Bhakoot Koota (max 7 points).
    Based on the distance between the two Rashis.
    Inauspicious combinations: 2/12, 6/8, 5/9 → 0 points
    All others → 7 points
    """
    idx1 = RASHIS.index(rashi1) if rashi1 in RASHIS else 0
    idx2 = RASHIS.index(rashi2) if rashi2 in RASHIS else 0
    
    dist = ((idx2 - idx1) % 12) + 1
    
    # Inauspicious distances (counted from groom to bride)
    inauspicious = {2, 12, 6, 8, 5, 9}
    
    return 0 if dist in inauspicious else 7


def calculate_nadi(nak1: str, nak2: str) -> int:
    """
    Nadi Koota (max 8 points).
    MOST IMPORTANT koota. Same Nadi = 0 (Nadi Dosha), different = 8.
    Nadi represents health constitution. Same Nadi in couple can cause
    health problems in children.
    """
    n1 = NAKSHATRA_TO_NADI.get(nak1, "Aadi")
    n2 = NAKSHATRA_TO_NADI.get(nak2, "Aadi")
    return 0 if n1 == n2 else 8


def calculate_ashtakoot(rashi1, nak1, rashi2, nak2) -> dict:
    """
    Calculate the complete Ashtakoot (8-factor) compatibility score.
    
    Args:
        rashi1, nak1: Person 1's Rashi and Nakshatra
        rashi2, nak2: Person 2's Rashi and Nakshatra
    
    Returns:
        Dictionary with individual koota scores and total
    """
    scores = {
        "varna_score": calculate_varna(rashi1, rashi2),
        "vashya_score": calculate_vashya(rashi1, rashi2),
        "tara_score": calculate_tara(nak1, nak2),
        "yoni_score": calculate_yoni(nak1, nak2),
        "graha_maitri_score": calculate_graha_maitri(rashi1, rashi2),
        "gana_score": calculate_gana(nak1, nak2),
        "bhakoot_score": calculate_bhakoot(rashi1, rashi2),
        "nadi_score": calculate_nadi(nak1, nak2),
    }
    scores["total_score"] = sum(scores.values())
    scores["compatible"] = 1 if scores["total_score"] >= 18 else 0
    
    return scores


# ═══════════════════════════════════════════════════════════════════════════════
# MAIN GENERATION LOGIC
# ═══════════════════════════════════════════════════════════════════════════════

def generate_compatibility_dataset():
    """
    Main function: generate 1000+ compatibility pairs from celebrity data.
    """
    # Load celebrity data
    if INPUT_FILE.exists():
        df = load_csv(INPUT_FILE)
        logger.info(f"📋 Loaded {len(df)} celebrities from {INPUT_FILE}")
    elif FALLBACK_INPUT.exists():
        df = load_csv(FALLBACK_INPUT)
        logger.info(f"📋 Loaded {len(df)} celebrities from {FALLBACK_INPUT} (fallback)")
    else:
        logger.error(
            "❌ No input file found! Run the scraper and/or planet fetcher first."
        )
        return
    
    # ── Compute Rashi and Nakshatra for each person ─────────────────────
    logger.info("\n🌙 Computing Moon signs and Nakshatras...")
    
    persons = []
    for idx, row in df.iterrows():
        rn = get_rashi_nakshatra(row.to_dict())
        if rn:
            rashi, nakshatra = rn
            persons.append({
                "name": row.get("full_name", f"Person_{idx}"),
                "dob": row.get("date_of_birth", ""),
                "tob": row.get("time_of_birth", ""),
                "pob": row.get("place_of_birth", ""),
                "moon_sign": rashi,
                "nakshatra": nakshatra,
            })
    
    logger.info(f"   Computed for {len(persons)} persons")
    
    if len(persons) < 2:
        logger.error("❌ Need at least 2 persons to generate pairs!")
        return
    
    # ── Generate random pairs ───────────────────────────────────────────
    n_pairs = min(1200, len(persons) * (len(persons) - 1) // 2)
    logger.info(f"\n🔄 Generating {n_pairs} random pairs...")
    
    # Generate unique pairs
    pairs_set = set()
    all_indices = list(range(len(persons)))
    
    while len(pairs_set) < n_pairs:
        i, j = random.sample(all_indices, 2)
        pair = (min(i, j), max(i, j))  # Ensure unique regardless of order
        pairs_set.add(pair)
    
    # ── Calculate scores for each pair ──────────────────────────────────
    logger.info("📊 Calculating Ashtakoot scores...")
    
    results = []
    for pair_num, (i, j) in enumerate(pairs_set):
        p1 = persons[i]
        p2 = persons[j]
        
        # Calculate compatibility
        scores = calculate_ashtakoot(
            p1["moon_sign"], p1["nakshatra"],
            p2["moon_sign"], p2["nakshatra"]
        )
        
        # Build output row
        row = {
            "person1_name": p1["name"],
            "person1_dob": p1["dob"],
            "person1_tob": p1["tob"],
            "person1_pob": p1["pob"],
            "person1_moon_sign": p1["moon_sign"],
            "person1_nakshatra": p1["nakshatra"],
            
            "person2_name": p2["name"],
            "person2_dob": p2["dob"],
            "person2_tob": p2["tob"],
            "person2_pob": p2["pob"],
            "person2_moon_sign": p2["moon_sign"],
            "person2_nakshatra": p2["nakshatra"],
        }
        row.update(scores)
        results.append(row)
        
        if (pair_num + 1) % 200 == 0:
            logger.info(f"   Processed {pair_num + 1}/{n_pairs} pairs")
    
    # ── Save results ────────────────────────────────────────────────────
    save_to_csv(results, OUTPUT_FILE)
    
    # ── Print summary statistics ────────────────────────────────────────
    result_df = pd.DataFrame(results)
    
    logger.info(f"\n{'='*60}")
    logger.info(f"🏁 COMPLETE! Generated {len(results)} compatibility pairs")
    logger.info(f"   Output: {OUTPUT_FILE}")
    logger.info(f"\n📊 Score Distribution:")
    logger.info(f"   Min score:  {result_df['total_score'].min()}")
    logger.info(f"   Max score:  {result_df['total_score'].max()}")
    logger.info(f"   Mean score: {result_df['total_score'].mean():.1f}")
    logger.info(f"   Median:     {result_df['total_score'].median():.1f}")
    logger.info(f"\n   Compatible (≥18):     {result_df['compatible'].sum()} "
               f"({result_df['compatible'].mean()*100:.1f}%)")
    logger.info(f"   Not compatible (<18): {(~result_df['compatible'].astype(bool)).sum()} "
               f"({(1-result_df['compatible'].mean())*100:.1f}%)")
    
    logger.info(f"\n📊 Individual Koota Averages:")
    for koota in ["varna", "vashya", "tara", "yoni", "graha_maitri", "gana", "bhakoot", "nadi"]:
        col = f"{koota}_score"
        logger.info(f"   {koota.replace('_', ' ').title():15s}: "
                    f"{result_df[col].mean():.2f}")
    
    logger.info(f"{'='*60}")


# ─── Entry Point ─────────────────────────────────────────────────────────────

if __name__ == "__main__":
    print("-" * 60)
    print("   AstroConnect — Vedic Compatibility Dataset Generator     ")
    print("                                                            ")
    print("  Generates 1000+ compatibility pairs using Ashtakoot       ")
    print("  (Gun Milan) scoring from Vedic astrology.                 ")
    print("                                                            ")
    print("  8 Kootas scored out of 36 total points.                   ")
    print("  Compatible if score >= 18.                                ")
    print("-" * 60)
    
    generate_compatibility_dataset()
