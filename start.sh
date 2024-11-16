#!/bin/bash

# Navigate to the project directory
cd /c:/Users/Mattheo/Documents/Dev/S5/Projet/Elixir-Delices

# Start the local PHP server
php -S localhost:8000 &

# Start MySQL server (assuming you have MySQL installed and configured)
# Adjust the path to the MySQL executable if necessary
mysqld --console &

echo "Local PHP server started at http://localhost:8000"
echo "MySQL server started"