#!/bin/bash

# Colors for better UX
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
MAGENTA='\033[0;35m'
CYAN='\033[0;36m'
WHITE='\033[1;37m'
NC='\033[0m' # No Color

# Function to display the main header
show_header() {
    clear
    echo -e "${CYAN}"
    echo " ██████╗██████╗  ██████╗ "
    echo "██╔════╝██╔══██╗██╔═══██╗"
    echo "███████╗██║  ██║██║   ██║"
    echo "╚════██║██║  ██║██║   ██║"
    echo "██████╔╝██████╔╝╚██████╔╝"
    echo "╚═════╝ ╚═════╝  ╚═════╝ "
    echo -e "${NC}"
    echo -e "${MAGENTA}🔗 GitHub: https://github.com/StarDeception/SDO${NC}"
    echo -e "${WHITE}========== StarDeception Game Server Manager ==========${NC}"
    echo
}

# Function to display the main menu
show_main_menu() {
    echo -e "${YELLOW}Please select an option:${NC}"
    echo
    echo -e "${GREEN}1)${NC} Create new servers"
    echo -e "${GREEN}2)${NC} Delete all servers"
    echo -e "${GREEN}3)${NC} Start all servers"
    echo -e "${BLUE}4)${NC} Stop all servers"
    echo -e "${CYAN}5)${NC} Check servers status (soon)"
    echo -e "${RED}6)${NC} Exit"
    echo
    echo -n "Enter your choice [1-6]: "
}

# Function to check and download the dedicated server binary
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
        read -p "Press Enter to return to main menu..."
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
            echo -e "${RED}No URL provided. Returning to main menu.${NC}"
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
        echo -e "${YELLOW}Download cancelled. Returning to main menu.${NC}"
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
        read -p "Press Enter to return to main menu..."
        return 1
    fi
    
    if [[ "$download_success" == false ]]; then
        echo -e "${RED}✗ Download failed. Please check the URL and your internet connection.${NC}"
        read -p "Press Enter to return to main menu..."
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

# Function to handle server creation
create_servers() {
    show_header
    echo -e "${BLUE}=== Create New Servers ===${NC}"
    echo
    
    if [[ -f "./scripts/create_servers.sh" ]]; then
        # Ensure script has execute permissions
        chmod +x "./scripts/create_servers.sh"
        echo -e "${BLUE}Launching server creation script...${NC}"
        echo
        ./scripts/create_servers.sh
    else
        echo -e "${RED}✗ scripts/create_servers.sh not found${NC}"
        read -p "Press Enter to return to main menu..."
        return 1
    fi
    
    echo
    read -p "Press Enter to return to main menu..."
}

# Function to handle server deletion
delete_servers() {
    show_header
    echo -e "${RED}=== Delete All Servers ===${NC}"
    echo
    
    if [[ -f "./scripts/delete_servers.sh" ]]; then
        # Ensure script has execute permissions
        chmod +x "./scripts/delete_servers.sh"
        echo -e "${YELLOW}⚠ Warning: This will delete all server directories${NC}"
        echo
        ./scripts/delete_servers.sh
    else
        echo -e "${RED}✗ scripts/delete_servers.sh not found${NC}"
        read -p "Press Enter to return to main menu..."
        return 1
    fi
    
    echo
    read -p "Press Enter to return to main menu..."
}

# Function to handle stopping all servers
stop_all_servers() {
    show_header
    echo -e "${BLUE}=== Stop All Servers ===${NC}"
    echo
    
    if [[ -f "./scripts/stop_all_servers.sh" ]]; then
        # Ensure script has execute permissions
        chmod +x "./scripts/stop_all_servers.sh"
        echo -e "${BLUE}Launching server stop script...${NC}"
        echo
        ./scripts/stop_all_servers.sh
    else
        echo -e "${YELLOW}scripts/stop_all_servers.sh not found. Using direct approach...${NC}"
        echo
        
        # Check for running servers
        running_servers=$(ps aux 2>/dev/null | grep -v grep | grep "StarDeception.dedicated_server" | wc -l)
        
        if [ $running_servers -eq 0 ]; then
            echo -e "${YELLOW}No StarDeception servers are currently running.${NC}"
        else
            echo -e "${BLUE}Found $running_servers running server(s)${NC}"
            echo -e "${YELLOW}Do you want to stop all StarDeception servers?${NC}"
            read -p "Enter [y/N]: " confirm
            
            if [[ $confirm =~ ^[Yy]$ ]]; then
                echo -e "${BLUE}Stopping all servers...${NC}"
                pkill -f "StarDeception.dedicated_server" 2>/dev/null
                sleep 2
                echo -e "${GREEN}✓ Stop command sent to all servers${NC}"
            else
                echo -e "${YELLOW}Operation cancelled.${NC}"
            fi
        fi
    fi
    
    echo
    read -p "Press Enter to return to main menu..."
}

# Function to handle starting all servers
start_all_servers() {
    show_header
    echo -e "${GREEN}=== Start All Servers ===${NC}"
    echo
    
    # First check and download the binary if needed
    if ! check_and_download_binary; then
        return 1
    fi
    
    if [[ -f "./scripts/start_all_servers.sh" ]]; then
        # Ensure script has execute permissions
        chmod +x "./scripts/start_all_servers.sh"
        echo -e "${BLUE}Launching server startup script...${NC}"
        echo
        ./scripts/start_all_servers.sh
    else
        echo -e "${RED}✗ scripts/start_all_servers.sh not found${NC}"
        read -p "Press Enter to return to main menu..."
        return 1
    fi
    
    echo
    read -p "Press Enter to return to main menu..."
}

# Function to check server status
check_servers_status() {
    show_header
    echo -e "${CYAN}=== Check Servers Status ===${NC}"
    echo
    
    if [[ -f "./scripts/status_servers.sh" ]]; then
        # Ensure script has execute permissions
        chmod +x "./scripts/status_servers.sh"
        echo -e "${BLUE}Launching server status check...${NC}"
        echo
        ./scripts/status_servers.sh
    else
        echo -e "${RED}✗ scripts/status_servers.sh not found${NC}"
        read -p "Press Enter to return to main menu..."
        return 1
    fi
    
    echo
    read -p "Press Enter to return to main menu..."
}

# Main program loop
main() {
    while true; do
        show_header
        show_main_menu
        
        read choice
        
        case $choice in
            1)
                create_servers
                ;;
            2)
                delete_servers
                ;;
            3)
                start_all_servers
                ;;
            4)
                stop_all_servers
                ;;
            5)
                check_servers_status
                ;;
            6)
                echo
                echo -e "${CYAN}Thank you for using StarDeception Game Server Manager!${NC}"
                echo -e "${CYAN}Goodbye!${NC}"
                exit 0
                ;;
            *)
                echo
                echo -e "${RED}Invalid option. Please select 1-6.${NC}"
                sleep 2
                ;;
        esac
    done
}

# Check if we're in the right directory
if [[ ! -f "./scripts/create_servers.sh" ]] || [[ ! -f "./scripts/delete_servers.sh" ]] || [[ ! -f "./scripts/start_all_servers.sh" ]]; then
    echo -e "${RED}✗ Error: Required scripts not found in scripts/ directory${NC}"
    echo "Please make sure you're running this script from the Foundry directory"
    echo "and that scripts/create_servers.sh, scripts/delete_servers.sh, and scripts/start_all_servers.sh exist."
    exit 1
fi

# Start the main program
main
