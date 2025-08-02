#!/bin/bash

# Colors for better UX
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Script to set executable permissions for all shell scripts
echo -e "${CYAN}========== Setting Up Permissions ==========${NC}"
echo

# Set permissions for main script
chmod +x ../StarDeception_GameServer.sh
echo -e "${GREEN}✓ ../StarDeception_GameServer.sh${NC}"

# Set permissions for scripts in scripts directory
chmod +x create_servers.sh
echo -e "${GREEN}✓ create_servers.sh${NC}"

chmod +x delete_servers.sh
echo -e "${GREEN}✓ delete_servers.sh${NC}"

chmod +x start_all_servers.sh
echo -e "${GREEN}✓ start_all_servers.sh${NC}"

chmod +x stop_all_servers.sh
echo -e "${GREEN}✓ stop_all_servers.sh${NC}"

chmod +x check_servers.sh
echo -e "${GREEN}✓ check_servers.sh${NC}"

chmod +x repair_servers.sh
echo -e "${GREEN}✓ repair_servers.sh${NC}"

chmod +x status_servers.sh
echo -e "${GREEN}✓ status_servers.sh${NC}"

# Set permissions for setup permissions script itself
chmod +x setup_permissions.sh
echo -e "${GREEN}✓ setup_permissions.sh${NC}"

# Set permissions for dedicated server script
chmod +x ../src/StarDeception.dedicated_server.sh
echo -e "${GREEN}✓ ../src/StarDeception.dedicated_server.sh${NC}"

echo
echo -e "${BLUE}All shell scripts now have executable permissions!${NC}"
