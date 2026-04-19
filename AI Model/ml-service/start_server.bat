@echo off
set "BASE_DIR=%~dp0"
echo ==================================================
echo    ASTROCONNECT COMPATIBILITY API SERVER
echo ==================================================
echo.

call "%BASE_DIR%venv\Scripts\activate.bat"

echo Starting FastAPI with Uvicorn on http://localhost:8001
echo Press Ctrl+C to stop the server.
echo.
uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload

pause
