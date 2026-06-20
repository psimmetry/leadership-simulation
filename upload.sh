#!/bin/bash

# --- CONFIGURATION ---
# This matches the "Host" nickname you set up in your ~/.ssh/config file
SSH_ALIAS="psical" 
# The default directory if you don't specify a second argument
DEFAULT_DEST="~/copied-from-marco/"
# ---------------------

# Verify at least the first argument (local file) is provided
if [ -z "$1" ]; then
    echo "Usage: $0 <local_file_or_folder> [remote_destination_folder]"
    exit 1
fi

LOCAL_PATH="$1"

# If the second argument ($2) is provided, use it. Otherwise, fall back to default.
if [ -n "$2" ]; then
    REMOTE_DEST="$2"
else
    REMOTE_DEST="$DEFAULT_DEST"
fi

# Check if the local file/folder actually exists
if [ ! -e "$LOCAL_PATH" ]; then
    echo "Error: Local path '$LOCAL_PATH' does not exist."
    exit 1
fi

echo "🚀 Uploading '$LOCAL_PATH' to $SSH_ALIAS:$REMOTE_DEST..."

# Run scp (-r handles both files and directories)
scp -r "$LOCAL_PATH" "$SSH_ALIAS:$REMOTE_DEST"

# Check if the scp command succeeded
if [ $? -eq 0 ]; then
    echo "✅ Upload complete!"
else
    echo "❌ Upload failed. Check your connection or remote path."
fi