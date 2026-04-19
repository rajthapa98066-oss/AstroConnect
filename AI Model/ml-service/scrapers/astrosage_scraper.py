"""
astrosage_scraper.py — Scrape celebrity birth data from AstroSage.com

This script visits AstroSage's Celebrity Horoscope database, iterates through
multiple profession categories, and scrapes each celebrity's:
  - Full name
  - Date of birth
  - Time of birth
  - Place of birth

It uses Selenium (for JavaScript-rendered pages) plus BeautifulSoup (for parsing).
Results are saved incrementally to data/raw/celebrities_raw.csv.

Usage:
    python -m scrapers.astrosage_scraper
    
    Or from the ml-service directory:
    python scrapers/astrosage_scraper.py
"""

import re
import sys
import time
import random
from pathlib import Path

import pandas as pd
from bs4 import BeautifulSoup
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import (
    TimeoutException, NoSuchElementException, WebDriverException
)
from webdriver_manager.chrome import ChromeDriverManager

# Add parent directory to path so we can import utils
sys.path.insert(0, str(Path(__file__).resolve().parent.parent))
from scrapers.utils import setup_logger, append_row_to_csv, DATA_RAW

# ─── Configuration ────────────────────────────────────────────────────────────

# AstroSage celebrity categories to scrape
CATEGORIES = [
    "Cricket",
    "Sports",
    "Football",
    "Hockey",
    "Bollywood",
    "Hollywood",
    "Politician",
    "Businessman",
    "Musician",
    "Singer",
    "Literature",
    "Scientist",
]

# How many celebrities to scrape per category (adjust as needed)
# Set higher numbers if you want more data; scraping will stop
# when either the target is hit or all pages are exhausted
TARGET_PER_CATEGORY = {
    "Cricket":     80,
    "Sports":      30,
    "Football":    30,
    "Hockey":      20,
    "Bollywood":   60,
    "Hollywood":   60,
    "Politician":  80,
    "Businessman": 70,
    "Musician":    40,
    "Singer":      30,
    "Literature":  50,
    "Scientist":   50,
}

BASE_URL = "https://www.astrosage.com/celebrity-horoscope/default.asp"
OUTPUT_FILE = DATA_RAW / "celebrities_raw.csv"

logger = setup_logger("scraper", str(DATA_RAW / "scraper.log"))


# ─── Selenium Setup ──────────────────────────────────────────────────────────

def create_driver() -> webdriver.Chrome:
    """
    Create a headless Chrome browser instance.
    Uses webdriver_manager to automatically download the correct ChromeDriver.
    
    Returns:
        Configured Chrome WebDriver instance
    """
    options = Options()
    options.add_argument("--headless=new")          # Run without opening a window
    options.add_argument("--disable-gpu")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--window-size=1920,1080")
    options.add_argument(
        "user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) "
        "AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
    )
    # Suppress logging noise
    options.add_experimental_option("excludeSwitches", ["enable-logging"])
    
    service = Service(ChromeDriverManager().install())
    driver = webdriver.Chrome(service=service, options=options)
    driver.set_page_load_timeout(30)
    return driver


# ─── Scraping Functions ──────────────────────────────────────────────────────

def get_celebrity_links_from_page(driver, url: str) -> list[str]:
    """
    Extract all celebrity profile links from a category listing page.
    
    AstroSage uses RELATIVE URLs on listing pages like:
    'virat-kohli-horoscope.asp'
    """
    CELEBRITY_BASE = "https://www.astrosage.com/celebrity-horoscope/"
    
    try:
        driver.get(url)
        time.sleep(random.uniform(2, 4))
        
        soup = BeautifulSoup(driver.page_source, "lxml")
        links = []
        
        # Target links that end in -horoscope.asp AND have a title attribute
        for a_tag in soup.select('a[href$="-horoscope.asp"][title]'):
            href = a_tag["href"].strip()
            
            # ── 1. Exclude utility/navigation keywords ──────────────────
            exclude_keywords = [
                "today", "tomorrow", "weekly", "monthly", "yearly", 
                "online", "suggested", "submit", "default", "rashifal",
                "rashiphal", "horoscope-prediction"
            ]
            if any(k in href.lower() for k in exclude_keywords):
                continue
            
            # ── 2. Exclude non-celebrity paths ──────────────────────────
            if "/horoscope/" in href.lower():
                continue
                
            # ── 3. Build full URL ───────────────────────────────────────
            if href.startswith("http"):
                full_url = href
            elif href.startswith("/"):
                # If absolute path like /celebrity-horoscope/alia-bhatt-horoscope.asp
                full_url = f"https://www.astrosage.com{href}"
            else:
                # Relative URL — prepend the celebrity horoscope base
                # Ensure we don't double the path
                if "celebrity-horoscope" in href:
                    full_url = f"https://www.astrosage.com/{href.lstrip('/')}"
                else:
                    full_url = f"{CELEBRITY_BASE}{href}"
            
            if full_url not in links:
                links.append(full_url)
        
        logger.info(f"  Found {len(links)} celebrity links on page")
        return links
        
    except Exception as e:
        logger.error(f"  Error loading page {url}: {e}")
        return []


def scrape_celebrity_profile(driver, url: str) -> dict | None:
    """
    Visit a celebrity's profile page and extract birth details.
    
    AstroSage stores birth data in 'div.celebcont' blocks:
    <div><b>Date of Birth:</b> Saturday, November 05, 1988</div>
    
    Args:
        driver: Selenium WebDriver
        url: Celebrity profile page URL
    
    Returns:
        Dictionary with celebrity data, or None if essential data is missing
    """
    try:
        driver.get(url)
        time.sleep(random.uniform(2, 3.5))
        
        soup = BeautifulSoup(driver.page_source, "lxml")
        
        # ── 1. Extract Name ─────────────────────────────────────────────
        name = "Unknown"
        h1 = soup.find("h1")
        if h1:
            name = h1.get_text(strip=True)
            for suffix in ["Birth Chart", "Horoscope", "Kundli", "Birth"]:
                name = name.replace(suffix, "").strip().rstrip("/").strip()
        
        # ── 2. Extract Details from celebcont divs ──────────────────────
        details = {}
        cont_divs = soup.select("div.celebcont")
        
        for div in cont_divs:
            b_tag = div.find("b")
            if not b_tag:
                continue
                
            label = b_tag.get_text(strip=True).replace(":", "").lower()
            # Extract text after the label
            value = div.get_text(strip=True).replace(b_tag.get_text(strip=True), "").strip()
            
            if "date of birth" in label:
                details["dob"] = value
            elif "time of birth" in label:
                details["tob"] = value
            elif "place of birth" in label:
                details["pob"] = value
            elif "rating" in label or "accuracy" in label:
                details["accuracy"] = value
            elif "source" in label:
                details["source"] = value

        # ── 3. Fallback (Regex) if div parsing failed ───────────────────
        if not details.get("dob") or not details.get("tob"):
            page_text = soup.get_text(separator="\n")
            lines = page_text.split("\n")
            for line in lines:
                line_clean = line.strip()
                if not details.get("dob"):
                    m = re.search(r"Date of Birth\s*[:\-]?\s*(.*)", line_clean, re.I)
                    if m: details["dob"] = m.group(1).strip()
                if not details.get("tob"):
                    m = re.search(r"Time of Birth\s*[:\-]?\s*(.*)", line_clean, re.I)
                    if m: details["tob"] = m.group(1).strip()
                if not details.get("pob"):
                    m = re.search(r"Place of Birth\s*[:\-]?\s*(.*)", line_clean, re.I)
                    if m: details["pob"] = m.group(1).strip()

        # ── 4. Validation & Cleaning ────────────────────────────────────
        dob = details.get("dob")
        tob = details.get("tob")
        pob = details.get("pob", "Unknown")
        
        # Clean the date (remove weekday like 'Monday, ')
        if dob and "," in dob:
            parts = dob.split(",")
            if len(parts) > 1:
                dob = ",".join(parts[1:]).strip()
        
        # Skip if essential data is missing OR if time is default 00:00:00 with "Dirty" source
        if not tob or not dob:
            return None
            
        # Optional: Filter out low-quality data (00:00:00 usually means unknown time)
        if tob == "00:00:00" and ("dirty" in str(details.get("source")).lower() or "not known" in str(details.get("source")).lower()):
            logger.debug(f"  ⏭ Skipping {name} — time of birth is likely unknown (00:00:00)")
            return None

        return {
            "full_name": name,
            "date_of_birth": dob,
            "time_of_birth": tob,
            "place_of_birth": pob,
            "astrosage_url": url,
            "accuracy_rating": details.get("accuracy", "N/A"),
            "data_source": details.get("source", "N/A")
        }
        
    except Exception as e:
        logger.error(f"  ❌ Error scraping {url}: {e}")
        return None


def get_total_pages(driver, category: str) -> int:
    """
    Determine how many pages of celebrities exist for a given category.
    
    Looks for pagination links like: [1] [2] [3] ... [Next »]
    
    Args:
        driver: Selenium WebDriver
        category: AstroSage category name (e.g., "Bollywood")
    
    Returns:
        Total number of pages (at least 1)
    """
    url = f"{BASE_URL}?category={category}"
    try:
        driver.get(url)
        time.sleep(2)
        
        soup = BeautifulSoup(driver.page_source, "lxml")
        
        # Find pagination links: ?page=N&category=...
        max_page = 1
        for a_tag in soup.find_all("a", href=True):
            href = a_tag["href"]
            page_match = re.search(r"page=(\d+)", href)
            if page_match:
                page_num = int(page_match.group(1))
                max_page = max(max_page, page_num)
        
        return max_page
        
    except Exception as e:
        logger.error(f"Error getting page count for {category}: {e}")
        return 1


# ─── Main Scraping Loop ─────────────────────────────────────────────────────

def scrape_all_categories():
    """
    Main function: iterate through all categories, scrape celebrity data,
    and save results incrementally to CSV.
    """
    # Load existing data to avoid re-scraping
    already_scraped = set()
    if OUTPUT_FILE.exists():
        existing_df = pd.read_csv(OUTPUT_FILE, encoding="utf-8-sig")
        already_scraped = set(existing_df["astrosage_url"].values)
        logger.info(f"📋 Found {len(already_scraped)} already scraped celebrities")
    
    driver = create_driver()
    total_scraped = len(already_scraped)
    
    try:
        for category in CATEGORIES:
            target = TARGET_PER_CATEGORY.get(category, 50)
            category_count = 0
            
            logger.info(f"\n{'='*60}")
            logger.info(f"📂 Category: {category}")
            logger.info(f"   Target: {target} celebrities")
            logger.info(f"{'='*60}")
            
            # Find how many pages this category has
            total_pages = get_total_pages(driver, category)
            logger.info(f"   Found {total_pages} pages")
            
            for page in range(1, total_pages + 1):
                if category_count >= target:
                    logger.info(f"   ✅ Reached target ({target}) for {category}")
                    break
                
                # Build the page URL
                if page == 1:
                    page_url = f"{BASE_URL}?category={category}"
                else:
                    page_url = f"{BASE_URL}?page={page}&category={category}"
                
                logger.info(f"\n  📄 Page {page}/{total_pages} — {page_url}")
                
                # Get all celebrity links from this listing page
                celebrity_links = get_celebrity_links_from_page(driver, page_url)
                
                if not celebrity_links:
                    logger.warning(f"  ⚠ No links found on page {page}")
                    continue
                
                # Visit each celebrity's profile
                for link in celebrity_links:
                    if category_count >= target:
                        break
                    
                    if link in already_scraped:
                        logger.debug(f"  ⏭ Already scraped: {link}")
                        continue
                    
                    # Scrape the profile
                    data = scrape_celebrity_profile(driver, link)
                    
                    if data:
                        # Save immediately (so we don't lose progress on crash)
                        append_row_to_csv(data, OUTPUT_FILE)
                        already_scraped.add(link)
                        category_count += 1
                        total_scraped += 1
                        
                        logger.info(
                            f"  ✅ [{total_scraped}] {data['full_name']} | "
                            f"DOB: {data['date_of_birth']} | "
                            f"TOB: {data['time_of_birth']}"
                        )
                    
                    # Random delay to be respectful
                    time.sleep(random.uniform(1.5, 3.0))
            
            logger.info(f"\n  📊 {category}: scraped {category_count} celebrities")
    
    except KeyboardInterrupt:
        logger.info("\n\n⚠ Scraping interrupted by user. Progress has been saved.")
    
    except Exception as e:
        logger.error(f"\n❌ Fatal error: {e}")
        import traceback
        traceback.print_exc()
    
    finally:
        driver.quit()
        logger.info(f"\n{'='*60}")
        logger.info(f"🏁 COMPLETE! Total celebrities scraped: {total_scraped}")
        logger.info(f"   Output file: {OUTPUT_FILE}")
        logger.info(f"{'='*60}")


# ─── Entry Point ─────────────────────────────────────────────────────────────

if __name__ == "__main__":
    print("-" * 60)
    print("       AstroConnect — AstroSage Celebrity Scraper          ")
    print("                                                           ")
    print("  This will scrape celebrity birth data from AstroSage.    ")
    print("  Target: ~500+ celebrities across multiple categories.    ")
    print("  Estimated time: 2-4 hours (with respectful rate limits). ")
    print("                                                           ")
    print("  Press Ctrl+C at any time to stop. Progress is saved.     ")
    print("-" * 60)
    
    scrape_all_categories()
