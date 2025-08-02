#!/bin/bash

# Colors for better UX
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${CYAN}========== Server Repair Tool ==========${NC}"
echo

# Check if source files exist
if [[ ! -f "src/StarDeception.dedicated_server.sh" ]] || [[ ! -f "src/StarDeception.dedicated_server.x86_64" ]]; then
    echo -e "${RED}✗ Source files missing in src/ directory${NC}"
    echo "Please make sure the following files exist:"
    echo "  - src/StarDeception.dedicated_server.sh"
    echo "  - src/StarDeception.dedicated_server.x86_64"
    exit 1
fi

echo -e "${GREEN}✓ Source files found${NC}"

# Find all server directories
server_dirs=($(find . -maxdepth 1 -type d -name "server*" | sort))

if [ ${#server_dirs[@]} -eq 0 ]; then
    echo -e "${YELLOW}No server directories found. Nothing to repair.${NC}"
    exit 0
fi

echo -e "${BLUE}Found ${#server_dirs[@]} server directories to repair...${NC}"
echo

repaired_count=0

for dir in "${server_dirs[@]}"; do
    echo -e "${CYAN}Repairing $dir...${NC}"
    
    # Copy missing files
    files_copied=0
    
    if [[ ! -f "$dir/StarDeception.dedicated_server.sh" ]]; then
        cp "src/StarDeception.dedicated_server.sh" "$dir/"
        echo -e "${GREEN}  ✓ Copied StarDeception.dedicated_server.sh${NC}"
        ((files_copied++))
    else
        # Update existing file in case source was updated
        cp "src/StarDeception.dedicated_server.sh" "$dir/"
        echo -e "${BLUE}  ✓ Updated StarDeception.dedicated_server.sh${NC}"
    fi
    
    if [[ ! -f "$dir/StarDeception.dedicated_server.x86_64" ]]; then
        cp "src/StarDeception.dedicated_server.x86_64" "$dir/"
        echo -e "${GREEN}  ✓ Copied StarDeception.dedicated_server.x86_64${NC}"
        ((files_copied++))
    fi
    
    # Set executable permissions
    chmod +x "$dir/StarDeception.dedicated_server.sh" 2>/dev/null
    chmod +x "$dir/StarDeception.dedicated_server.x86_64" 2>/dev/null
    echo -e "${GREEN}  ✓ Set executable permissions${NC}"
    
    # Fix line endings for shell scripts (convert Windows CRLF to Unix LF)
    if command -v dos2unix >/dev/null 2>&1; then
        dos2unix "$dir/StarDeception.dedicated_server.sh" 2>/dev/null
        echo -e "${GREEN}  ✓ Fixed line endings${NC}"
    elif command -v sed >/dev/null 2>&1; then
        sed -i 's/\r$//' "$dir/StarDeception.dedicated_server.sh" 2>/dev/null
        echo -e "${GREEN}  ✓ Fixed line endings${NC}"
    fi
    
    if [ $files_copied -gt 0 ]; then
        ((repaired_count++))
        echo -e "${GREEN}  ✓ $dir repaired${NC}"
    else
        echo -e "${BLUE}  ✓ $dir was already complete${NC}"
    fi
    
    echo
done

echo -e "${BLUE}Repair Summary:${NC}"
if [ $repaired_count -gt 0 ]; then
    echo -e "${GREEN}✓ Repaired $repaired_count server directories${NC}"
    echo -e "${BLUE}💡 All servers should now be ready to start!${NC}"
else
    echo -e "${BLUE}✓ All server directories were already complete${NC}"
fi
