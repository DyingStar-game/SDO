#!/bin/bash

# Colors for better UX
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Function to check if screen is installed
check_screen_installed() {
    if ! command -v screen &> /dev/null; then
        return 1
    else
        return 0
    fi
}

# Function to install screen based on the OS package manager
install_screen() {
    # Detect package manager
    if command -v apt &> /dev/null; then
        echo -e "${BLUE}Installing screen via apt...${NC}"
        sudo apt update && sudo apt install -y screen
    elif command -v dnf &> /dev/null; then
        echo -e "${BLUE}Installing screen via dnf...${NC}"
        sudo dnf install -y screen
    elif command -v yum &> /dev/null; then
        echo -e "${BLUE}Installing screen via yum...${NC}"
        sudo yum install -y screen
    elif command -v apk &> /dev/null; then
        echo -e "${BLUE}Installing screen via apk...${NC}"
        sudo apk add screen
    else
        echo -e "${RED}✗ Unable to determine package manager.${NC}"
        echo -e "${YELLOW}Please install 'screen' manually and run this script again.${NC}"
        echo -e "${BLUE}Common installation commands:${NC}"
        echo "  • Debian/Ubuntu: sudo apt install screen"
        echo "  • Fedora: sudo dnf install screen"
        echo "  • CentOS/RHEL: sudo yum install screen"
        echo "  • Alpine: sudo apk add screen"
        exit 1
    fi
}

# Check and install screen if necessary
if ! check_screen_installed; then
    echo -e "${YELLOW}⚠ The 'screen' package is not installed. It is required to run servers in the background.${NC}"
    echo -e "${BLUE}Attempting automatic installation...${NC}"
    install_screen
    
    # Check if installation was successful
    if ! check_screen_installed; then
        echo -e "${RED}✗ Installation of 'screen' failed.${NC}"
        exit 1
    else
        echo -e "${GREEN}✓ 'screen' has been successfully installed.${NC}"
    fi
else
    echo -e "${GREEN}✓ 'screen' is already installed.${NC}"
fi

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
        session_name="$(basename "$dir")_session"
        echo -e "${BLUE}Starting server in $dir (screen: $session_name)...${NC}"
        cd "$dir"
        chmod +x StarDeception.dedicated_server.sh
        # Démarre le serveur dans une session screen détachée
        screen -dmS "$session_name" bash -c './StarDeception.dedicated_server.sh > server.log 2>&1'
        if screen -list | grep -q "$session_name"; then
            echo -e "${GREEN}  ✓ Server started in screen session: $session_name${NC}"
            ((started_count++))
        else
            echo -e "${RED}  ✗ Failed to start server in screen session: $session_name${NC}"
        fi
        cd "$current_dir"
        sleep 1
    else
        echo -e "${YELLOW}  ⚠ Warning: StarDeception.dedicated_server.sh not found in $dir${NC}"
    fi
done

echo
if [ $started_count -gt 0 ]; then
    echo -e "${GREEN}✓ Successfully started $started_count server(s)!${NC}"
    echo -e "${BLUE}📋 Server management tips:${NC}"
    echo "  • Check individual server.log files in each server directory for output"
    echo "  • To stop all servers: for s in $(screen -ls | grep '_session' | awk '{print $1}'); do screen -S \"$s\" -X quit; done"
    echo "  • To check running servers: screen -ls | grep '_session'"
    echo "  • To attach to a server: screen -r <session_name> (ex: screen -r server1_session)"
else
    echo -e "${RED}✗ No servers were started${NC}"
fi
