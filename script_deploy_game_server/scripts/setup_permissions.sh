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

# Script to set executable permissions for all shell scripts
echo -e "${CYAN}========== Setting Up Permissions ==========${NC}"
echo

# Set permissions for main script
chmod +x StarDeception_GameServer.sh
echo -e "${GREEN}✓ StarDeception_GameServer.sh${NC}"

# Set permissions for scripts in scripts directory
chmod +x scripts/create_servers.sh
echo -e "${GREEN}✓ scripts/create_servers.sh${NC}"

chmod +x scripts/delete_servers.sh
echo -e "${GREEN}✓ scripts/delete_servers.sh${NC}"

chmod +x scripts/start_all_servers.sh
echo -e "${GREEN}✓ scripts/start_all_servers.sh${NC}"

chmod +x scripts/stop_all_servers.sh
echo -e "${GREEN}✓ scripts/stop_all_servers.sh${NC}"

# Set permissions for setup permissions script itself
chmod +x scripts/setup_permissions.sh
echo -e "${GREEN}✓ scripts/setup_permissions.sh${NC}"

# Set permissions for dedicated server script
chmod +x src/StarDeception.dedicated_server.sh
echo -e "${GREEN}✓ src/StarDeception.dedicated_server.sh${NC}"

echo
echo -e "${BLUE}All shell scripts now have executable permissions!${NC}"
