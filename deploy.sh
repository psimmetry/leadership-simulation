#!/bin/bash

# exit on non-zero status
set -e

echo "SSH-ing in..."
ssh psimmetry-leadership

echo "Navigating to repo..."
cd ~/leaderhip-simulation

echo "Pulling main"
git pull

echo "Cleaning ~/htdocs"
rm -rf ~/htdocs

echo "Copying new ./htdocs files to ~/htdocs"
cp -r ./htdocs ~/htdocs

echo "Finished!
exit 1