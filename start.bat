@echo off
REM Start PHP built-in server
php -S localhost:8000

REM Start MySQL server (assuming you have MySQL installed and configured)
net start MySQL

pause