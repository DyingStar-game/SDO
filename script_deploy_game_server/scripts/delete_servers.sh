#!/bin/bash

# Colors for better UX
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${RED}========== Server Cleanup Tool ==========${NC}"
echo

read -p "Do you really want to delete all server* folders? (y/n) " confirm
if [[ "$confirm" =~ ^[Yy]$ ]]; then
  echo -e "${YELLOW}Deleting all server directories...${NC}"
  rm -rf server*
  echo -e "${GREEN}✓ All server* folders have been deleted.${NC}"
else
  echo -e "${CYAN}Operation cancelled.${NC}"
fi
