#!/bin/bash

# Colors for better UX
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${BLUE}========== Stop All Game Servers ==========${NC}"
echo

# Check for running StarDeception servers in screen sessions
running_servers=$(screen -ls | grep "_session" | wc -l)

if [ $running_servers -eq 0 ]; then
    echo -e "${YELLOW}No StarDeception servers are currently running.${NC}"
    exit 0
fi

echo -e "${BLUE}Found $running_servers running StarDeception server(s)${NC}"
echo

# Show running screen sessions
echo -e "${BLUE}Currently running servers:${NC}"
screen -ls | grep "_session" | while read line; do
    echo "  $line"
done
echo

# Ask for confirmation
echo -e "${YELLOW}Do you want to stop all StarDeception servers?${NC}"
read -p "Enter [y/N]: " confirm

if [[ ! $confirm =~ ^[Yy]$ ]]; then
    echo -e "${YELLOW}Operation cancelled.${NC}"
    exit 0
fi

echo -e "${BLUE}Stopping all StarDeception servers...${NC}"

# Stop the servers by terminating screen sessions
stopped_count=0
screen_sessions=($(screen -ls | grep "_session" | awk '{print $1}'))

for session in "${screen_sessions[@]}"; do
    echo -e "  Stopping session: $session"
    screen -S "$session" -X quit
    ((stopped_count++))
done

# Wait a moment for graceful shutdown
sleep 2

# Check if any servers are still running
remaining_servers=$(screen -ls | grep "_session" | wc -l)

if [ $remaining_servers -eq 0 ]; then
    echo -e "${GREEN}✓ All StarDeception servers (${stopped_count}) have been stopped successfully.${NC}"
else
    echo -e "${YELLOW}⚠ Some servers are still running. Attempting forceful termination...${NC}"
    # Get any remaining session names
    remaining_sessions=$(screen -ls | grep "_session" | awk '{print $1}')
    
    # Forcefully terminate any remaining sessions
    for session in $remaining_sessions; do
        echo -e "  Force stopping session: $session"
        screen -S "$session" -X quit
    done
    
    sleep 1
    
    final_check=$(screen -ls | grep "_session" | wc -l)
    if [ $final_check -eq 0 ]; then
        echo -e "${GREEN}✓ All servers have been forcefully stopped.${NC}"
    else
        echo -e "${RED}✗ Some servers could not be stopped. Please check manually.${NC}"
    fi
fi

echo
echo -e "${BLUE}📋 Server management tips:${NC}"
echo "  • Check server.log files in each server directory for shutdown logs"
echo "  • To start servers again: ./StarDeception_GameServer.sh"
echo "  • To check for any remaining screen sessions: screen -ls | grep '_session'"
echo "  • To manually terminate a screen session: screen -S <session_name> -X quit"
