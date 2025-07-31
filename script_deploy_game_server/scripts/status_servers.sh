#!/bin/bash

# Colors for better UX
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${CYAN}========== Server Status Check ==========${NC}"
echo

# Check for running StarDeception servers
running_processes=$(ps aux | grep -v grep | grep "StarDeception.dedicated_server")
running_count=$(echo "$running_processes" | grep -c "StarDeception.dedicated_server" 2>/dev/null || echo "0")

if [ $running_count -eq 0 ]; then
    echo -e "${YELLOW}No StarDeception servers are currently running.${NC}"
else
    echo -e "${GREEN}Found $running_count running StarDeception server(s):${NC}"
    echo
    echo -e "${BLUE}Running processes:${NC}"
    echo "$running_processes" | while IFS= read -r line; do
        if [[ -n "$line" ]]; then
            pid=$(echo "$line" | awk '{print $2}')
            echo -e "${GREEN}  PID: $pid${NC} - $line"
        fi
    done
fi

echo

# Check server directories and their log files
server_dirs=($(find . -maxdepth 1 -type d -name "server*" | sort))

if [ ${#server_dirs[@]} -eq 0 ]; then
    echo -e "${YELLOW}No server directories found.${NC}"
else
    echo -e "${BLUE}Server directories status:${NC}"
    for dir in "${server_dirs[@]}"; do
        echo -e "${CYAN}$dir:${NC}"
        
        # Check if log file exists and show last few lines
        if [[ -f "$dir/server.log" ]]; then
            log_size=$(wc -l < "$dir/server.log" 2>/dev/null || echo "0")
            echo -e "${GREEN}  ✓ Log file exists ($log_size lines)${NC}"
            
            # Show last 3 lines of log
            echo -e "${BLUE}  Last log entries:${NC}"
            tail -n 3 "$dir/server.log" 2>/dev/null | while IFS= read -r line; do
                echo "    $line"
            done
        else
            echo -e "${RED}  ✗ No log file found${NC}"
        fi
        
        # Check if server files exist
        if [[ -f "$dir/StarDeception.dedicated_server.sh" ]]; then
            echo -e "${GREEN}  ✓ Server script present${NC}"
        else
            echo -e "${RED}  ✗ Server script missing${NC}"
        fi
        
        if [[ -f "$dir/StarDeception.dedicated_server.x86_64" ]]; then
            echo -e "${GREEN}  ✓ Server binary present${NC}"
        else
            echo -e "${RED}  ✗ Server binary missing${NC}"
        fi
        
        echo
    done
fi

echo -e "${BLUE}💡 Tips:${NC}"
echo "  • To stop all servers: pkill -f StarDeception.dedicated_server"
echo "  • To view full logs: tail -f server*/server.log"
echo "  • To restart servers: Stop them first, then start again"
