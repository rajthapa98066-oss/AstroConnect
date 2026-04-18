"""
fetch_planets.py — Fetch planetary positions from AstrologyAPI for each celebrity.

For each celebrity in the scraped dataset, this script:
1. Parses their birth date/time
2. Geocodes their birth place to lat/lon
3. Calls the AstrologyAPI /planets/extended endpoint
4. Extracts positions for all 11 celestial bodies
5. Merges the data and saves to data/raw/celebrities_with_planets.csv

API: https://astrologyapi.com/
Auth: HTTP Basic Auth (userId:apiKey Base64-encoded)
Endpoint: POST https://json.astrologyapi.com/v1/planets/extended

Usage:
    python -m scrapers.fetch_planets
"""

import os
import sys
import time
import json
import base64
import requests
from pathlib import Path

import pandas as pd
from dotenv import load_dotenv

# Add parent directory to path
sys.path.insert(0, str(Path(__file__).resolve().parent.parent))
from scrapers.utils import (
    setup_logger, append_row_to_csv, load_csv,
    GeocoderWithCache, get_timezone_offset,
    parse_date, parse_time, DATA_RAW, BASE_DIR
)

# ─── Configuration ────────────────────────────────────────────────────────────

# Load API credentials from .env file
load_dotenv(BASE_DIR / ".env")

API_USER_ID = os.getenv("ASTROLOGY_API_USER_ID")
API_KEY = os.getenv("ASTROLOGY_API_KEY")
API_URL = "https://json.astrologyapi.com/v1/planets/extended"

INPUT_FILE = DATA_RAW / "celebrities_raw.csv"
OUTPUT_FILE = DATA_RAW / "celebrities_with_planets.csv"

# Planets we want to extract data for
PLANETS = [
    "Sun", "Moon", "Mars", "Mercury", "Jupiter",
    "Venus", "Saturn", "Uranus", "Neptune",
    "Rahu", "Ketu"
]

logger = setup_logger("fetch_planets", str(DATA_RAW / "fetch_planets.log"))


# ─── API Functions ───────────────────────────────────────────────────────────

def create_auth_header() -> dict:
    """
    Create HTTP Basic Auth header for AstrologyAPI.
    
    Encodes 'userId:apiKey' as Base64 and returns the Authorization header.
    
    Returns:
        Dictionary with Authorization and Content-Type headers
    """
    if not API_USER_ID or not API_KEY:
        raise ValueError(
            "API credentials not found! Please set ASTROLOGY_API_USER_ID "
            "and ASTROLOGY_API_KEY in your .env file."
        )
    
    credentials = f"{API_USER_ID}:{API_KEY}"
    encoded = base64.b64encode(credentials.encode()).decode()
    
    return {
        "Authorization": f"Basic {encoded}",
        "Content-Type": "application/json",
    }


def fetch_planet_positions(
    day: int, month: int, year: int,
    hour: int, minute: int,
    lat: float, lon: float,
    tzone: float
) -> dict | None:
    """
    Call the AstrologyAPI planets/extended endpoint.
    
    Args:
        day, month, year: Birth date components
        hour, minute: Birth time in 24-hour format
        lat, lon: Latitude and longitude of birth place
        tzone: Timezone offset in hours (e.g., 5.5 for IST)
    
    Returns:
        API response as dict, or None on failure
    """
    headers = create_auth_header()
    
    payload = {
        "day": day,
        "month": month,
        "year": year,
        "hour": hour,
        "min": minute,
        "lat": round(lat, 4),
        "lon": round(lon, 4),
        "tzone": tzone,
    }
    
    max_retries = 3
    
    for attempt in range(max_retries):
        try:
            response = requests.post(
                API_URL,
                headers=headers,
                json=payload,
                timeout=30,
            )
            
            if response.status_code == 200:
                return response.json()
            
            elif response.status_code == 429:
                # Rate limited — wait and retry
                wait_time = (attempt + 1) * 10
                logger.warning(f"  ⏳ Rate limited. Waiting {wait_time}s...")
                time.sleep(wait_time)
                continue
            
            elif response.status_code == 401:
                logger.error("  ❌ Authentication failed! Check your API credentials.")
                return None
            
            else:
                logger.warning(
                    f"  ⚠ API returned status {response.status_code}: "
                    f"{response.text[:200]}"
                )
                if attempt < max_retries - 1:
                    time.sleep(5)
                    continue
                return None
                
        except requests.exceptions.Timeout:
            logger.warning(f"  ⏱ Request timed out (attempt {attempt + 1})")
            time.sleep(5)
            
        except requests.exceptions.RequestException as e:
            logger.error(f"  ❌ Request error: {e}")
            time.sleep(5)
    
    return None


def extract_planet_data(api_response: list | dict) -> dict:
    """
    Extract relevant planetary data from the API response.
    
    The API returns a list of planet objects. For each planet, we extract:
    - sign: The zodiac sign (e.g., "Scorpio")
    - degree: Position in degrees (e.g., 12.5)
    - house: Astrological house number (1-12)
    - is_retrograde: Whether the planet is in retrograde
    
    Returns:
        Flat dictionary with columns like sun_sign, sun_degree, etc.
    """
    result = {}
    
    # The API returns a list of planet objects
    if isinstance(api_response, list):
        planet_list = api_response
    elif isinstance(api_response, dict) and "planets" in api_response:
        planet_list = api_response["planets"]
    else:
        # If structure is unexpected, return empty
        logger.warning(f"  ⚠ Unexpected API response format: {type(api_response)}")
        return result
    
    # Create a lookup by planet name
    planet_lookup = {}
    for planet_data in planet_list:
        name = planet_data.get("name", "").strip()
        planet_lookup[name] = planet_data
    
    # Extract data for each planet we care about
    for planet_name in PLANETS:
        prefix = planet_name.lower().replace(" ", "_")
        
        # Try different name formats: SUN, Sun, sun
        data = (
            planet_lookup.get(planet_name.upper()) or
            planet_lookup.get(planet_name.capitalize()) or
            planet_lookup.get(planet_name.lower()) or
            {}
        )
        
        if data:
            result[f"{prefix}_sign"] = data.get("sign", "")
            result[f"{prefix}_degree"] = data.get("normDegree", data.get("fullDegree", ""))
            result[f"{prefix}_house"] = data.get("house", "")
            
            # Handle string booleans: "true"/"false" -> True/False
            retro = data.get("isRetro", data.get("is_retro", False))
            if isinstance(retro, str):
                retro = retro.lower() == "true"
            result[f"{prefix}_retrograde"] = retro
        else:
            result[f"{prefix}_sign"] = ""
            result[f"{prefix}_degree"] = ""
            result[f"{prefix}_house"] = ""
            result[f"{prefix}_retrograde"] = ""
    
    return result


# ─── Main Processing Loop ───────────────────────────────────────────────────

def process_all_celebrities():
    """
    Main function: load scraped celebrities, fetch planetary data for each,
    and save the enriched dataset.
    """
    # Load input data
    if not INPUT_FILE.exists():
        logger.error(
            f"❌ Input file not found: {INPUT_FILE}\n"
            f"   Run the scraper first: python -m scrapers.astrosage_scraper"
        )
        return
    
    df = load_csv(INPUT_FILE)
    logger.info(f"📋 Loaded {len(df)} celebrities from {INPUT_FILE}")
    
    # Load already-processed entries to avoid re-fetching
    already_processed = set()
    if OUTPUT_FILE.exists():
        existing = load_csv(OUTPUT_FILE)
        already_processed = set(existing["astrosage_url"].values)
        logger.info(f"   {len(already_processed)} already processed")
    
    # Initialize geocoder
    geocoder = GeocoderWithCache()
    
    success_count = 0
    skip_count = 0
    error_count = 0
    
    for idx, row in df.iterrows():
        url = row.get("astrosage_url", "")
        name = row.get("full_name", "Unknown")
        
        # Skip if already processed
        if url in already_processed:
            skip_count += 1
            continue
        
        logger.info(f"\n[{idx + 1}/{len(df)}] Processing: {name}")
        
        # ── Parse birth date ────────────────────────────────────────────
        date_parsed = parse_date(str(row.get("date_of_birth", "")))
        if not date_parsed:
            logger.warning(f"  ⏭ Cannot parse DOB: {row.get('date_of_birth')}")
            error_count += 1
            continue
        day, month, year = date_parsed
        
        # ── Parse birth time ────────────────────────────────────────────
        time_parsed = parse_time(str(row.get("time_of_birth", "")))
        if not time_parsed:
            logger.warning(f"  ⏭ Cannot parse TOB: {row.get('time_of_birth')}")
            error_count += 1
            continue
        hour, minute = time_parsed
        
        # ── Geocode birth place ─────────────────────────────────────────
        place = str(row.get("place_of_birth", ""))
        coords = geocoder.geocode(place)
        
        if not coords:
            # Try with just the country part
            if "," in place:
                coords = geocoder.geocode(place.split(",")[-1].strip())
            if not coords:
                # Default to New Delhi for Indian celebrities
                logger.warning(f"  ⚠ Geocoding failed for '{place}', using Delhi default")
                coords = (28.6139, 77.2090)
        
        lat, lon = coords
        tzone = get_timezone_offset(lat, lon)
        
        logger.info(
            f"  📍 {place} → ({lat:.2f}, {lon:.2f}) TZ: {tzone}"
        )
        
        # ── Call the API ────────────────────────────────────────────────
        api_response = fetch_planet_positions(
            day, month, year, hour, minute, lat, lon, tzone
        )
        
        if not api_response:
            logger.warning(f"  ⏭ API returned no data for {name}")
            error_count += 1
            continue
        
        # ── Extract planet data ─────────────────────────────────────────
        planet_data = extract_planet_data(api_response)
        
        if not planet_data:
            logger.warning(f"  ⏭ Could not extract planet data for {name}")
            error_count += 1
            continue
        
        # ── Merge celebrity data + planet data ──────────────────────────
        combined = row.to_dict()
        combined.update(planet_data)
        combined["latitude"] = lat
        combined["longitude"] = lon
        combined["timezone"] = tzone
        
        # Save immediately
        append_row_to_csv(combined, OUTPUT_FILE)
        already_processed.add(url)
        success_count += 1
        
        logger.info(
            f"  ✅ Moon: {planet_data.get('moon_sign', '?')} | "
            f"Sun: {planet_data.get('sun_sign', '?')} | "
            f"Asc: house {planet_data.get('sun_house', '?')}"
        )
        
        # Rate limiting: AstrologyAPI allows reasonable usage
        time.sleep(1.0)
        
        # Save progress summary every 10 records
        if success_count % 10 == 0:
            logger.info(f"\n  📊 Progress: {success_count} fetched, "
                       f"{skip_count} skipped, {error_count} errors\n")
    
    # ── Final Summary ───────────────────────────────────────────────────
    logger.info(f"\n{'='*60}")
    logger.info(f"🏁 COMPLETE!")
    logger.info(f"   ✅ Successfully fetched: {success_count}")
    logger.info(f"   ⏭ Skipped (already done): {skip_count}")
    logger.info(f"   ❌ Errors: {error_count}")
    logger.info(f"   Output: {OUTPUT_FILE}")
    logger.info(f"{'='*60}")


# ─── Entry Point ─────────────────────────────────────────────────────────────

if __name__ == "__main__":
    print("-" * 60)
    print("    AstroConnect — Planetary Position Fetcher               ")
    print("                                                            ")
    print("  Fetches planetary data from AstrologyAPI for each         ")
    print("  celebrity in celebrities_raw.csv.                         ")
    print("                                                            ")
    print("  Requires: ASTROLOGY_API_USER_ID and ASTROLOGY_API_KEY     ")
    print("  in the .env file.                                         ")
    print("-" * 60)
    
    process_all_celebrities()
