"""
utils.py — Shared utility functions for AstroConnect ML data collection pipeline.
Includes: logging, CSV helpers, rate limiting, geocoding cache.
"""

import os
import json
import time
import random
import logging
import functools
from pathlib import Path

import pandas as pd
from geopy.geocoders import Nominatim
from geopy.exc import GeocoderTimedOut, GeocoderServiceError

# ─── Paths ────────────────────────────────────────────────────────────────────
BASE_DIR = Path(__file__).resolve().parent.parent          # ml-service/
DATA_RAW = BASE_DIR / "data" / "raw"
DATA_PROCESSED = BASE_DIR / "data" / "processed"
GEOCODE_CACHE_FILE = BASE_DIR / "scrapers" / "geocode_cache.json"

# Ensure directories exist
DATA_RAW.mkdir(parents=True, exist_ok=True)
DATA_PROCESSED.mkdir(parents=True, exist_ok=True)
(BASE_DIR / "models").mkdir(parents=True, exist_ok=True)
(BASE_DIR / "notebooks").mkdir(parents=True, exist_ok=True)


# ─── Logging ──────────────────────────────────────────────────────────────────
def setup_logger(name: str, log_file: str = None) -> logging.Logger:
    """
    Create a logger that prints to console and optionally a file.
    
    Args:
        name: Name of the logger (usually __name__)
        log_file: Optional path to a .log file
    
    Returns:
        Configured logging.Logger instance
    """
    logger = logging.getLogger(name)
    logger.setLevel(logging.DEBUG)
    
    # Console handler
    ch = logging.StreamHandler()
    ch.setLevel(logging.INFO)
    fmt = logging.Formatter("[%(asctime)s] %(levelname)s - %(message)s", "%H:%M:%S")
    ch.setFormatter(fmt)
    logger.addHandler(ch)
    
    # File handler (optional)
    if log_file:
        fh = logging.FileHandler(log_file, encoding="utf-8")
        fh.setLevel(logging.DEBUG)
        fh.setFormatter(logging.Formatter(
            "[%(asctime)s] %(levelname)s - %(message)s", "%Y-%m-%d %H:%M:%S"
        ))
        logger.addHandler(fh)
    
    return logger


# ─── CSV helpers ──────────────────────────────────────────────────────────────
def save_to_csv(data: list[dict], filepath: str | Path, mode: str = "w"):
    """
    Save a list of dicts to CSV. 
    'w' = overwrite, 'a' = append (no header on append).
    
    Args:
        data: List of dictionaries to save
        filepath: Output CSV file path
        mode: 'w' for write (with header), 'a' for append (no header)
    """
    df = pd.DataFrame(data)
    header = (mode == "w")
    df.to_csv(filepath, mode=mode, header=header, index=False, encoding="utf-8-sig")


def load_csv(filepath: str | Path) -> pd.DataFrame:
    """Load a CSV file into a pandas DataFrame."""
    return pd.read_csv(filepath, encoding="utf-8-sig")


def append_row_to_csv(row: dict, filepath: str | Path):
    """
    Append a single row to a CSV file.
    If the file doesn't exist, create it with headers.
    
    Args:
        row: Dictionary representing one row of data
        filepath: Path to the CSV file
    """
    filepath = Path(filepath)
    file_exists = filepath.exists() and filepath.stat().st_size > 0
    df = pd.DataFrame([row])
    df.to_csv(filepath, mode="a", header=not file_exists, index=False, encoding="utf-8-sig")


# ─── Rate Limiting ────────────────────────────────────────────────────────────
def rate_limit(min_delay: float = 2.0, max_delay: float = 4.0):
    """
    Decorator that adds a random delay before each function call.
    Helps avoid getting blocked by websites/APIs.
    
    Args:
        min_delay: Minimum seconds to wait
        max_delay: Maximum seconds to wait
    """
    def decorator(func):
        @functools.wraps(func)
        def wrapper(*args, **kwargs):
            delay = random.uniform(min_delay, max_delay)
            time.sleep(delay)
            return func(*args, **kwargs)
        return wrapper
    return decorator


# ─── Geocoding (place name → lat/lon) ────────────────────────────────────────
class GeocoderWithCache:
    """
    Geocodes place names to (latitude, longitude) with a JSON file cache.
    Uses OpenStreetMap's Nominatim service (free, no API key needed).
    
    The cache avoids repeated API calls for the same place, which:
    - Speeds up processing significantly
    - Respects Nominatim's rate limits
    """
    
    def __init__(self):
        self.geolocator = Nominatim(user_agent="astroconnect_fyp_research")
        self.cache = self._load_cache()
    
    def _load_cache(self) -> dict:
        """Load geocoding cache from JSON file."""
        if GEOCODE_CACHE_FILE.exists():
            with open(GEOCODE_CACHE_FILE, "r", encoding="utf-8") as f:
                return json.load(f)
        return {}
    
    def _save_cache(self):
        """Save geocoding cache to JSON file."""
        with open(GEOCODE_CACHE_FILE, "w", encoding="utf-8") as f:
            json.dump(self.cache, f, indent=2, ensure_ascii=False)
    
    def geocode(self, place_name: str) -> tuple[float, float] | None:
        """
        Convert a place name to (latitude, longitude).
        
        Args:
            place_name: e.g., "New Delhi, India"
        
        Returns:
            (lat, lon) tuple, or None if geocoding fails
        """
        if not place_name or place_name.strip() == "":
            return None
        
        # Normalize the key
        key = place_name.strip().lower()
        
        # Check cache first
        if key in self.cache:
            cached = self.cache[key]
            if cached is None:
                return None
            return (cached["lat"], cached["lon"])
        
        # Call Nominatim API
        try:
            time.sleep(1.1)  # Nominatim requires max 1 request/second
            location = self.geolocator.geocode(place_name, timeout=10)
            
            if location:
                result = {"lat": location.latitude, "lon": location.longitude}
                self.cache[key] = result
                self._save_cache()
                return (location.latitude, location.longitude)
            else:
                self.cache[key] = None
                self._save_cache()
                return None
                
        except (GeocoderTimedOut, GeocoderServiceError) as e:
            print(f"  ⚠ Geocoding failed for '{place_name}': {e}")
            return None


def get_timezone_offset(lat: float, lon: float) -> float:
    """
    Get timezone offset (in hours) for a given latitude/longitude.
    Uses timezonefinder library.
    
    Args:
        lat: Latitude
        lon: Longitude
    
    Returns:
        Timezone offset in hours (e.g., 5.5 for IST)
    """
    try:
        from timezonefinder import TimezoneFinder
        import pytz
        from datetime import datetime
        
        tf = TimezoneFinder()
        tz_name = tf.timezone_at(lat=lat, lng=lon)
        
        if tz_name:
            tz = pytz.timezone(tz_name)
            # Get the UTC offset (using a reference date to handle DST)
            offset = tz.utcoffset(datetime(2000, 1, 1))
            if offset:
                return offset.total_seconds() / 3600.0
        
        return 5.5  # Default to IST if timezone lookup fails
        
    except Exception:
        return 5.5  # Default to IST


def parse_date(date_str: str) -> tuple[int, int, int] | None:
    """
    Parse a date string into (day, month, year).
    Handles multiple formats: 'November 5, 1988', '05/11/1988', etc.
    
    Returns:
        (day, month, year) tuple or None if parsing fails
    """
    from datetime import datetime
    
    formats = [
        "%B %d, %Y",      # November 5, 1988
        "%b %d, %Y",      # Nov 5, 1988
        "%d %B %Y",       # 5 November 1988
        "%d %b %Y",       # 5 Nov 1988
        "%d/%m/%Y",       # 05/11/1988
        "%m/%d/%Y",       # 11/05/1988
        "%Y-%m-%d",       # 1988-11-05
        "%d-%m-%Y",       # 05-11-1988
        "%B %d,%Y",       # November 5,1988 (no space)
    ]
    
    if not date_str:
        return None
    
    date_str = date_str.strip()
    
    for fmt in formats:
        try:
            dt = datetime.strptime(date_str, fmt)
            return (dt.day, dt.month, dt.year)
        except ValueError:
            continue
    
    return None


def parse_time(time_str: str) -> tuple[int, int] | None:
    """
    Parse a time string into (hour_24, minute).
    Handles: '7:08 PM', '19:08', '7:08 AM', etc.
    
    Returns:
        (hour, minute) in 24-hour format, or None if parsing fails
    """
    from datetime import datetime
    
    if not time_str:
        return None
    
    time_str = time_str.strip()
    
    formats = [
        "%I:%M %p",   # 7:08 PM
        "%I:%M%p",    # 7:08PM
        "%H:%M",      # 19:08
        "%I:%M:%S %p",# 7:08:00 PM
        "%H:%M:%S",   # 19:08:00
    ]
    
    for fmt in formats:
        try:
            dt = datetime.strptime(time_str, fmt)
            return (dt.hour, dt.minute)
        except ValueError:
            continue
    
    return None
