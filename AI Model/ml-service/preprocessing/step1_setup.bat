@echo off
set "BASE_DIR=%~dp0.."
echo Setting up AstroConnect ML Environment...

echo 1. Activating virtual environment...
call "%BASE_DIR%\venv\Scripts\activate.bat"

echo 2. Installing requirements...
pip install -r "%BASE_DIR%\requirements.txt"

echo Setup Complete!
pause
