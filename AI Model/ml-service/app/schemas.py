from pydantic import BaseModel, Field

class CompatibilityRequest(BaseModel):
    person1_moon_sign: str = Field(description="Must exactly match e.g. 'Aries'")
    person1_nakshatra: str = Field(description="Must exactly match e.g. 'Ashwini'")
    person2_moon_sign: str = Field(description="Must exactly match e.g. 'Taurus'")
    person2_nakshatra: str = Field(description="Must exactly match e.g. 'Rohini'")
    varna_score: int
    vashya_score: int
    tara_score: float
    yoni_score: int
    graha_maitri_score: float
    gana_score: int
    bhakoot_score: int
    nadi_score: int

class CareerRequest(BaseModel):
    sun_sign: str
    moon_sign: str
    mars_sign: str
    mercury_sign: str
    jupiter_sign: str
    venus_sign: str
    saturn_sign: str
    rahu_sign: str
    ketu_sign: str
