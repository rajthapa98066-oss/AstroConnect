@echo off
set "BASE_DIR=%~dp0"
echo ==================================================
echo    ASTROCONNECT MACHINE LEARNING API SERVER
echo ==================================================
echo.
echo 1. Start FastAPI server
echo 2. Career prediction console UI (manual signs or DOB/time/lat/lon)
echo.
set /p CHOICE=Select option (1 or 2): 
echo.

call "%BASE_DIR%venv\Scripts\activate.bat"

if "%CHOICE%"=="1" (
	echo Starting FastAPI with Uvicorn on http://localhost:8001
	echo Press Ctrl+C to stop the server.
	echo.
	uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload
) else if "%CHOICE%"=="2" (
	echo Starting interactive career prediction console...
	echo.
	python -m app.console_career_ui
) else (
	echo Invalid option. Please run this script again and choose 1 or 2.
)

pause
