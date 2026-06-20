#!/bin/bash

# exit on non-zero status
set -e

echo "SSH-ing in..."
ssh psimmetry-leadership << 'EOF'
    set -e
    echo "Navigating to repo..."
    cd ~/leadership-simulation

    echo "Pulling main"
    git pull

    echo "Cleaning ~/htdocs"
    rm -rf ~/htdocs

    echo "Copying new ./htdocs files to ~/htdocs"
    cp -r htdocs ~/htdocs

    echo "Finished!"
EOF