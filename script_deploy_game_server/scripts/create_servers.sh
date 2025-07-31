#!/bin/bash

# Colors for better UX
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Get the directory where this script is located
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
# Change to the parent directory (script_deploy_game_server)
cd "$(dirname "$SCRIPT_DIR")"

# Function to check and download binary
check_and_download_binary() {
    local src_dir="./src"
    local binary_file="$src_dir/StarDeception.dedicated_server.x86_64"
    local link_file="$src_dir/StarDeception.dedicated_server_link.txt"
    
    echo -e "${BLUE}Checking for dedicated server binary...${NC}"
    
    # Check if binary already exists
    if [[ -f "$binary_file" ]]; then
        echo -e "${GREEN}✓ Dedicated server binary found${NC}"
        return 0
    fi
    
    echo -e "${YELLOW}⚠ Dedicated server binary not found${NC}"
    echo
    
    # Check if link file exists
    if [[ ! -f "$link_file" ]]; then
        echo -e "${RED}✗ Link file not found: $link_file${NC}"
        echo "Please make sure the link file exists with a valid download URL."
        read -p "Press Enter to continue..."
        return 1
    fi
    
    # Extract download link from file
    local download_url=$(grep -E "^https?://" "$link_file" | head -1)
    
    if [[ -z "$download_url" || "$download_url" == *"????????????"* ]]; then
        echo -e "${RED}✗ No valid download URL found in link file${NC}"
        echo
        echo "Please provide a valid download URL for the dedicated server binary:"
        read -p "Enter URL: " user_url
        
        if [[ -z "$user_url" ]]; then
            echo -e "${RED}No URL provided. Cannot create servers without binary.${NC}"
            read -p "Press Enter to continue..."
            return 1
        fi
        
        download_url="$user_url"
        
        # Update the link file with the new URL
        echo -e "${BLUE}Updating link file with provided URL...${NC}"
        sed -i "s|https://????????????|$download_url|g" "$link_file"
    fi
    
    echo -e "${BLUE}Found download URL: ${CYAN}$download_url${NC}"
    echo
    
    # Ask for confirmation
    echo -e "${YELLOW}Do you want to download the dedicated server binary now?${NC}"
    read -p "Enter [y/N]: " confirm
    
    if [[ ! $confirm =~ ^[Yy]$ ]]; then
        echo -e "${YELLOW}Download cancelled. Cannot create servers without binary.${NC}"
        read -p "Press Enter to continue..."
        return 1
    fi
    
    # Create src directory if it doesn't exist
    mkdir -p "$src_dir"
    
    # Download the file
    echo -e "${BLUE}Downloading dedicated server binary...${NC}"
    
    # Try different download methods
    local temp_file="/tmp/stardeception_server_download"
    local download_success=false
    
    # Try wget first
    if command -v wget >/dev/null 2>&1; then
        echo -e "${BLUE}Using wget to download...${NC}"
        if wget -O "$temp_file" "$download_url" 2>/dev/null; then
            download_success=true
        fi
    # Try curl if wget is not available
    elif command -v curl >/dev/null 2>&1; then
        echo -e "${BLUE}Using curl to download...${NC}"
        if curl -L -o "$temp_file" "$download_url" 2>/dev/null; then
            download_success=true
        fi
    else
        echo -e "${RED}✗ Neither wget nor curl found. Please install one of them.${NC}"
        read -p "Press Enter to continue..."
        return 1
    fi
    
    if [[ "$download_success" == false ]]; then
        echo -e "${RED}✗ Download failed. Please check the URL and your internet connection.${NC}"
        read -p "Press Enter to continue..."
        return 1
    fi
    
    # Move and rename the downloaded file
    mv "$temp_file" "$binary_file"
    
    # Make it executable
    chmod +x "$binary_file"
    
    echo -e "${GREEN}✓ Dedicated server binary downloaded and configured successfully${NC}"
    echo -e "${GREEN}✓ File saved as: $binary_file${NC}"
    echo -e "${GREEN}✓ Executable permissions set${NC}"
    echo
    
    return 0
}

echo -e "${CYAN}========== Game Server Creator ==========${NC}"
echo

# First check and download the binary if needed
if ! check_and_download_binary; then
    echo -e "${RED}Cannot proceed without the dedicated server binary.${NC}"
    exit 1
fi

echo

read -p "How many servers do you want to create? " count
read -p "Enter the gameserver identifier (2 digits, e.g., 12): " id
read -p "Enter the SDO IP address: " sdo_ip

# Quick validation
if ! [[ $id =~ ^[0-9]{2}$ ]]; then
  echo "Invalid identifier. It must be composed of 2 digits."
  exit 1
fi

port=7050

for ((i=1; i<=count; i++)); do
  folder="server$i"
  mkdir -p "$folder"
  
  # Server number format: 01, 02, ...
  num=$(printf "%02d" $i)

  cat > "$folder/server.ini" <<EOF
[server]
name="gameserver${id}${num}"
port=${port}
SDO="${sdo_ip}"
EOF

  # Copy the server files from src directory
  cp "src/StarDeception.dedicated_server.sh" "$folder/"
  cp "src/StarDeception.dedicated_server.x86_64" "$folder/"

  ((port++))
done

echo "$count servers created successfully."
