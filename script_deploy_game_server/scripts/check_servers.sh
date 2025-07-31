#!/bin/bash

# Colors for better UX
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${CYAN}========== Server Diagnostic Tool ==========${NC}"
echo

# Check if src files exist
echo -e "${BLUE}Checking source files...${NC}"
if [[ -f "src/StarDeception.dedicated_server.sh" ]]; then
    echo -e "${GREEN}✓ src/StarDeception.dedicated_server.sh exists${NC}"
else
    echo -e "${RED}✗ src/StarDeception.dedicated_server.sh missing${NC}"
fi

if [[ -f "src/StarDeception.dedicated_server.x86_64" ]]; then
    echo -e "${GREEN}✓ src/StarDeception.dedicated_server.x86_64 exists${NC}"
else
    echo -e "${RED}✗ src/StarDeception.dedicated_server.x86_64 missing${NC}"
fi

echo

# Find all server directories
server_dirs=($(find . -maxdepth 1 -type d -name "server*" | sort))

if [ ${#server_dirs[@]} -eq 0 ]; then
    echo -e "${YELLOW}No server directories found.${NC}"
    exit 0
fi

echo -e "${BLUE}Checking ${#server_dirs[@]} server directories...${NC}"
echo

missing_files=0

for dir in "${server_dirs[@]}"; do
    echo -e "${CYAN}Checking $dir:${NC}"
    
    # Check for server.ini
    if [[ -f "$dir/server.ini" ]]; then
        echo -e "${GREEN}  ✓ server.ini${NC}"
    else
        echo -e "${RED}  ✗ server.ini missing${NC}"
        ((missing_files++))
    fi
    
    # Check for StarDeception.dedicated_server.sh
    if [[ -f "$dir/StarDeception.dedicated_server.sh" ]]; then
        echo -e "${GREEN}  ✓ StarDeception.dedicated_server.sh${NC}"
        # Check if executable
        if [[ -x "$dir/StarDeception.dedicated_server.sh" ]]; then
            echo -e "${GREEN}    ✓ Executable${NC}"
        else
            echo -e "${YELLOW}    ⚠ Not executable${NC}"
        fi
    else
        echo -e "${RED}  ✗ StarDeception.dedicated_server.sh missing${NC}"
        ((missing_files++))
    fi
    
    # Check for StarDeception.dedicated_server.x86_64
    if [[ -f "$dir/StarDeception.dedicated_server.x86_64" ]]; then
        echo -e "${GREEN}  ✓ StarDeception.dedicated_server.x86_64${NC}"
        # Check if executable
        if [[ -x "$dir/StarDeception.dedicated_server.x86_64" ]]; then
            echo -e "${GREEN}    ✓ Executable${NC}"
        else
            echo -e "${YELLOW}    ⚠ Not executable${NC}"
        fi
    else
        echo -e "${RED}  ✗ StarDeception.dedicated_server.x86_64 missing${NC}"
        ((missing_files++))
    fi
    
    echo
done

echo -e "${BLUE}Summary:${NC}"
if [ $missing_files -eq 0 ]; then
    echo -e "${GREEN}✓ All server directories are properly configured!${NC}"
else
    echo -e "${RED}✗ Found $missing_files missing files${NC}"
    echo -e "${YELLOW}💡 Tip: Run the repair script to fix missing files${NC}"
fi
