from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from .schemas import CompatibilityRequest
from .services.prediction_service import prediction_service_instance

app = FastAPI(
    title="AstroConnect ML API", 
    description="Machine Learning Microservice for compatibility prediction.",
    version="1.0.0"
)

# Open up CORS for Laravel to access it cleanly
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"], # Change to Laravel URL in production
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get("/health")
def health_check():
    """Endpoint for Laravel to ping to verify ML server is alive online."""
    return {"status": "ok", "message": "AstroConnect ML Service is running smoothly."}

@app.post("/api/v1/predict/compatibility")
def get_compatibility(request: CompatibilityRequest):
    """
    Accepts Koota scores and Planetary signs, handles inline Vedic feature engineering,
    and replies with Random Forest Compatibility prediction.
    """
    try:
        data_dict = request.model_dump()
        result = prediction_service_instance.predict_compatibility(data_dict)
        return {"status": "success", "data": result}
    except Exception as e:
        raise HTTPException(status_code=400, detail=f"Prediction failed: {str(e)}")

if __name__ == "__main__":
    import uvicorn
    # Typically run via `uvicorn app.main:app --reload`
    uvicorn.run("app.main:app", host="0.0.0.0", port=8001, reload=True)
