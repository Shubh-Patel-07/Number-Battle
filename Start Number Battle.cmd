@echo off
setlocal
cd /d "%~dp0"
title Number Battle Server

echo Starting Number Battle...
echo Keep this window open while playing.
echo.
start "Number Battle" cmd /c "timeout /t 4 /nobreak >nul ^& start http://localhost:5173"
npm.cmd run dev
