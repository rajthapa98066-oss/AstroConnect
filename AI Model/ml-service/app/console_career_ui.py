import json
from typing import Dict, List

from app.services.prediction_service import prediction_service_instance
from scrapers.fetch_planets import extract_planet_data, fetch_planet_positions
from scrapers.utils import get_timezone_offset, parse_date, parse_time


def _format_feature_name(feature: str) -> str:
    return feature.replace("_", " ").replace("sign", "sign").title()


def _prompt_sign(feature: str, allowed_values: List[str]) -> str:
    label = _format_feature_name(feature)
    options_display = ", ".join(allowed_values)

    while True:
        print(f"\n{label}")
        print(f"Allowed signs: {options_display}")
        user_input = input("Enter sign: ").strip().title()

        if user_input in allowed_values:
            return user_input

        print("Invalid sign. Please enter one of the allowed signs exactly.")


def collect_career_inputs() -> Dict[str, str]:
    payload: Dict[str, str] = {}

    print("=" * 60)
    print("ASTROCONNECT CAREER PREDICTOR")
    print("=" * 60)
    print("Provide your planetary sign placements to get a career prediction.")

    for feature in prediction_service_instance.career_feature_order:
        encoder = prediction_service_instance.career_encoders[feature]
        allowed_values = sorted(map(str, encoder.classes_))
        payload[feature] = _prompt_sign(feature, allowed_values)

    return payload


def _prompt_float(label: str, min_value: float, max_value: float) -> float:
    while True:
        user_input = input(f"{label}: ").strip()
        try:
            value = float(user_input)
        except ValueError:
            print("Invalid number. Please enter a numeric value.")
            continue

        if min_value <= value <= max_value:
            return value

        print(f"Value must be between {min_value} and {max_value}.")


def _prompt_date_components() -> tuple[int, int, int]:
    while True:
        date_text = input("Date of birth (e.g., 1999-12-31 or November 5, 1988): ").strip()
        parsed = parse_date(date_text)
        if parsed:
            return parsed
        print("Invalid date format. Try: YYYY-MM-DD, DD/MM/YYYY, or Month Day, Year.")


def _prompt_time_components() -> tuple[int, int]:
    while True:
        time_text = input("Time of birth (e.g., 19:08 or 7:08 PM): ").strip()
        parsed = parse_time(time_text)
        if parsed:
            return parsed
        print("Invalid time format. Try: HH:MM (24h) or H:MM AM/PM.")


def collect_birth_inputs_and_derive_signs() -> Dict[str, str]:
    print("=" * 60)
    print("ASTROCONNECT CAREER PREDICTOR")
    print("=" * 60)
    print("Provide birth details to auto-calculate planetary signs.")

    day, month, year = _prompt_date_components()
    hour, minute = _prompt_time_components()
    lat = _prompt_float("Latitude (-90 to 90)", -90.0, 90.0)
    lon = _prompt_float("Longitude (-180 to 180)", -180.0, 180.0)

    timezone_offset = get_timezone_offset(lat, lon)
    print(f"Detected timezone offset: {timezone_offset}")

    api_response = fetch_planet_positions(
        day=day,
        month=month,
        year=year,
        hour=hour,
        minute=minute,
        lat=lat,
        lon=lon,
        tzone=timezone_offset,
    )

    if not api_response:
        raise RuntimeError(
            "Unable to fetch planetary positions. Check internet/API credentials in .env."
        )

    planet_data = extract_planet_data(api_response)
    if not planet_data:
        raise RuntimeError("Planet extraction failed. API response format may be invalid.")

    payload: Dict[str, str] = {}
    missing_features: List[str] = []

    for feature in prediction_service_instance.career_feature_order:
        sign_value = str(planet_data.get(feature, "")).strip().title()
        if not sign_value:
            missing_features.append(feature)
            continue
        payload[feature] = sign_value

    if missing_features:
        raise RuntimeError(
            "Missing required planetary signs from API: " + ", ".join(missing_features)
        )

    print("\nDerived planetary signs:")
    for feature in prediction_service_instance.career_feature_order:
        print(f"- {_format_feature_name(feature)}: {payload[feature]}")

    return payload


def _choose_input_mode() -> str:
    print("\nChoose input mode:")
    print("1. Enter planetary signs manually")
    print("2. Enter birth details (DOB, time, latitude, longitude)")

    while True:
        choice = input("Select option (1 or 2): ").strip()
        if choice in {"1", "2"}:
            return choice
        print("Invalid option. Please enter 1 or 2.")


def print_career_result(result: Dict) -> None:
    print("\n" + "=" * 60)
    print("PREDICTION RESULT")
    print("=" * 60)

    if result.get("is_uncertain"):
        print("Predicted Career: Uncertain")
        print(f"Suggested Career: {result.get('suggested_career', 'N/A')}")
        print(f"Reason: {result.get('uncertainty_reason', 'Low confidence')}")
    else:
        print(f"Predicted Career: {result.get('predicted_career', 'N/A')}")

    if "confidence_score" in result:
        print(f"Confidence Score: {result['confidence_score']:.2f}")

    top_predictions = result.get("top_predictions", [])
    if top_predictions:
        print("\nTop Predictions:")
        for idx, item in enumerate(top_predictions, start=1):
            career = item.get("career", "N/A")
            probability = float(item.get("probability", 0.0))
            print(f"{idx}. {career} ({probability:.2f})")

    print("\nRaw response:")
    print(json.dumps(result, indent=2))


def main() -> None:
    try:
        mode = _choose_input_mode()
        if mode == "1":
            payload = collect_career_inputs()
        else:
            payload = collect_birth_inputs_and_derive_signs()

        result = prediction_service_instance.predict_career(payload)
        print_career_result(result)
    except KeyboardInterrupt:
        print("\nOperation cancelled by user.")
    except Exception as exc:
        print(f"\nPrediction failed: {exc}")


if __name__ == "__main__":
    main()
