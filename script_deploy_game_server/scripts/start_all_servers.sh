#!/bin/bash

# Colors for better UX
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${GREEN}========== Start All Game Servers ==========${NC}"
echo

# Check if dedicated server binary exists
binary_file="./src/StarDeception.dedicated_server.x86_64"
if [[ ! -f "$binary_file" ]]; then
    echo -e "${RED}✗ Dedicated server binary not found: $binary_file${NC}"
    echo -e "${YELLOW}Please make sure the binary is downloaded and placed in the src/ directory.${NC}"
    echo -e "${BLUE}Tip: Use the main menu option to automatically download the binary.${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Dedicated server binary found${NC}"

# Find all server directories
server_dirs=($(find . -maxdepth 1 -type d -name "server*" | sort))

if [ ${#server_dirs[@]} -eq 0 ]; then
    echo -e "${RED}No server directories found. Please run create_servers.sh first.${NC}"
    exit 1
fi

echo -e "${BLUE}Found ${#server_dirs[@]} server(s) to start:${NC}"
for dir in "${server_dirs[@]}"; do
    echo "  - $dir"
done
echo

# Ask for confirmation
echo -e "${YELLOW}Do you want to start all servers?${NC}"
read -p "Enter [y/N]: " confirm
if [[ ! $confirm =~ ^[Yy]$ ]]; then
    echo -e "${YELLOW}Operation cancelled.${NC}"
    exit 0
fi

echo -e "${BLUE}Starting all servers...${NC}"
echo

# Start each server
started_count=0
current_dir=$(pwd)

for dir in "${server_dirs[@]}"; do
    if [ -f "$dir/StarDeception.dedicated_server.sh" ]; then
        echo -e "${BLUE}Starting server in $dir...${NC}"
        cd "$dir"
        chmod +x StarDeception.dedicated_server.sh
        nohup ./StarDeception.dedicated_server.sh > server.log 2>&1 &
        server_pid=$!
        echo -e "${GREEN}  ✓ Server started with PID: $server_pid${NC}"
        ((started_count++))
        cd "$current_dir"  # Return to original directory
        sleep 1  # Small delay between server starts
    else
        echo -e "${YELLOW}  ⚠ Warning: StarDeception.dedicated_server.sh not found in $dir${NC}"
    fi
done

echo
if [ $started_count -gt 0 ]; then
    echo -e "${GREEN}✓ Successfully started $started_count server(s)!${NC}"
    echo -e "${BLUE}📋 Server management tips:${NC}"
    echo "  • Check individual server.log files in each server directory for output"
    echo "  • To stop all servers: pkill -f StarDeception.dedicated_server"
    echo "  • To check running servers: ps aux | grep StarDeception"
else
    echo -e "${RED}✗ No servers were started${NC}"
fi
