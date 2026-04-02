#!/bin/bash

# Ai Song Maker - System Dependency Installer
# This script installs FFmpeg and FLAC tools needed for audio processing.

# Fail gracefully to avoid breaking automated deployment pipelines
# set -e

echo "----------------------------------------------------"
echo "Ai Song Maker: Checking system dependencies..."
echo "----------------------------------------------------"

# Check if running as root or have sudo access
if [ "$EUID" -ne 0 ] && ! command -v sudo &> /dev/null; then
    echo "Warning: This script may require root or sudo privileges to install packages."
fi

# Check for apt-get (Debian/Ubuntu)
if ! command -v apt-get &> /dev/null; then
    echo "Error: 'apt-get' not found. This script currently only supports Debian/Ubuntu-based systems."
    echo "If you are on a different OS (like CentOS or Alpine), please manually install 'ffmpeg' and 'flac' using your package manager (yum/dnf/apk)."
    exit 1
fi

# Function to check and install a package
check_and_install() {
    PACKAGE=$1
    BINARY=$2
    
    if command -v "$BINARY" &> /dev/null; then
        echo "[OK] $BINARY is already installed."
    else
        echo "[MISSING] $BINARY not found. Attempting to install $PACKAGE..."
        # Try to run with sudo if available, otherwise try directly
        if command -v sudo &> /dev/null; then
            sudo apt-get update -qq
            sudo apt-get install -y "$PACKAGE"
        else
            apt-get update -qq
            apt-get install -y "$PACKAGE"
        fi
        
        if command -v "$BINARY" &> /dev/null; then
            echo "[SUCCESS] $PACKAGE installed successfully."
        else
            echo "[FAILED] Could not install $PACKAGE. Please install it manually with 'sudo apt-get install $PACKAGE'."
        fi
    fi
}

# Install FFmpeg
check_and_install "ffmpeg" "ffmpeg"

# Install FLAC (provides metaflac needed by getID3 for FLAC files)
check_and_install "flac" "metaflac"

echo "----------------------------------------------------"
echo "Dependency check complete."
echo "----------------------------------------------------"
