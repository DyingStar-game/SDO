#!/bin/bash

# Colors for better UX
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Function to check if Docker is installed
check_docker_installed() {
    if ! command -v docker &> /dev/null; then
        return 1
    else
        return 0
    fi
}

# Function to create Dockerfile
create_dockerfile() {
    # Create the Dockerfile in the parent directory (script_deploy_game_server/)
    cat > Dockerfile << EOL
# Use a lightweight base image
FROM debian:latest

# Install necessary dependencies
RUN apt-get update && apt-get install -y procps && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /app

# Copy necessary files
# Note: Dockerfile is in script_deploy_game_server/, so src/ is relative to that
COPY src/StarDeception.dedicated_server.sh /app/
COPY src/StarDeception.dedicated_server.x86_64 /app/
# server.ini will be mounted as volume

# Make scripts executable
RUN chmod +x /app/StarDeception.dedicated_server.sh
RUN chmod +x /app/StarDeception.dedicated_server.x86_64

# Ports will be mapped at runtime

# Command to launch server
CMD ["./StarDeception.dedicated_server.sh"]
EOL
}

# Function to create and run a Docker container for a server
create_run_container() {
    local dir=$1
    local image_name=$2
    local server_name=$(basename "$dir")
    echo -e "${BLUE}Creating Docker container for $server_name...${NC}"
    
    # Get server port from server.ini
    local server_port=$(grep -oP "(?<=port=)[0-9]+" "$dir/server.ini" 2>/dev/null || echo "7050")
    
    # Check if container already exists
    if docker ps -a --format '{{.Names}}' | grep -q "^stardeception-$server_name$"; then
        echo -e "${YELLOW}  ⚠ Container already exists. Removing it...${NC}"
        docker rm -f "stardeception-$server_name" > /dev/null
    fi
    
    # Run Docker container
    docker run -d --name "stardeception-$server_name" \
        -p "$server_port:$server_port/tcp" \
        -p "$server_port:$server_port/udp" \
        -v "$dir/server.ini:/app/server.ini" \
        "$image_name"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}  ✓ Docker container started for $server_name on port $server_port${NC}"
        return 0
    else
        echo -e "${RED}  ✗ Failed to start Docker container for $server_name${NC}"
        return 1
    fi
}

# Function to install Docker based on the OS package manager
install_docker() {
    echo -e "${BLUE}Docker is not installed. Attempting automatic installation...${NC}"
    
    # Detect package manager
    if command -v apt &> /dev/null; then
        # Check if it's Ubuntu or Debian
        if grep -q "Ubuntu" /etc/os-release; then
            echo -e "${BLUE}Installing Docker on Ubuntu via apt...${NC}"
            sudo apt update
            sudo apt install -y ca-certificates curl
            sudo install -m 0755 -d /etc/apt/keyrings
            sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
            sudo chmod a+r /etc/apt/keyrings/docker.asc
            echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
            sudo apt update
            sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
        elif grep -q "Debian" /etc/os-release; then
            echo -e "${BLUE}Installing Docker on Debian via apt...${NC}"
            sudo apt update
            sudo apt install -y ca-certificates curl gnupg
            sudo install -m 0755 -d /etc/apt/keyrings
            sudo curl -fsSL https://download.docker.com/linux/debian/gpg -o /etc/apt/keyrings/docker.asc
            sudo chmod a+r /etc/apt/keyrings/docker.asc
            echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/debian $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
            sudo apt update
            sudo apt install -y docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
        else
            echo -e "${BLUE}Installing Docker on general Debian-based system via apt...${NC}"
            sudo apt update
            sudo apt install -y docker.io
        fi
    elif command -v dnf &> /dev/null; then
        echo -e "${BLUE}Installing Docker via dnf...${NC}"
        sudo dnf -y install dnf-plugins-core
        sudo dnf config-manager --add-repo https://download.docker.com/linux/fedora/docker-ce.repo
        sudo dnf install -y docker-ce docker-ce-cli containerd.io
    elif command -v yum &> /dev/null; then
        echo -e "${BLUE}Installing Docker via yum...${NC}"
        sudo yum install -y yum-utils
        sudo yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
        sudo yum install -y docker-ce docker-ce-cli containerd.io
    else
        echo -e "${RED}✗ Unable to determine package manager.${NC}"
        echo -e "${YELLOW}Please install 'Docker' manually and run this script again.${NC}"
        echo -e "${BLUE}Common installation commands:${NC}"
        echo "  • Debian/Ubuntu: https://docs.docker.com/engine/install/ubuntu/"
        echo "  • Fedora: https://docs.docker.com/engine/install/fedora/"
        echo "  • CentOS/RHEL: https://docs.docker.com/engine/install/centos/"
        exit 1
    fi

    # Start Docker service
    sudo systemctl start docker
    sudo systemctl enable docker
    
    # Add current user to docker group to avoid using sudo for docker commands
    sudo usermod -aG docker $(whoami)
    echo -e "${YELLOW}⚠ For group changes to take effect, please log out and log back in, or restart your session.${NC}"
}

# Check and install Docker if necessary
if ! check_docker_installed; then
    install_docker
    
    # Check if installation was successful
    if ! check_docker_installed; then
        echo -e "${RED}✗ Docker installation failed.${NC}"F
        exit 1
    else
        echo -e "${GREEN}✓ Docker has been successfully installed.${NC}"
    fi
else
    echo -e "${GREEN}✓ Docker is already installed.${NC}"
fi

echo -e "${GREEN}========== Docker Server Creation and Execution ==========${NC}"
echo

# Current working directory is: /workspaces/SDO/script_deploy_game_server/
# When the script is called by StarDeception_GameServer.sh

# Check if dedicated server binary exists
binary_file="src/StarDeception.dedicated_server.x86_64"
if [[ ! -f "$binary_file" ]]; then
    echo -e "${RED}✗ Dedicated server binary not found: $binary_file${NC}"
    echo -e "${YELLOW}Please make sure the binary is downloaded and placed in the src/ directory.${NC}"
    echo -e "${BLUE}Tip: Use the main menu option to automatically download the binary.${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Dedicated server binary found${NC}"

# Check if dedicated server script exists
script_file="src/StarDeception.dedicated_server.sh"
if [[ ! -f "$script_file" ]]; then
    echo -e "${RED}✗ Dedicated server script not found: $script_file${NC}"
    echo -e "${YELLOW}Please make sure the server script is in the src/ directory.${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Dedicated server script found${NC}"

# Find all server directories
server_dirs=($(find . -maxdepth 1 -type d -name "server*" | sort))

if [ ${#server_dirs[@]} -eq 0 ]; then
    echo -e "${RED}No server directories found. Please run create new servers first.${NC}"
    exit 1
fi

echo -e "${BLUE}Found ${#server_dirs[@]} server(s):${NC}"
for dir in "${server_dirs[@]}"; do
    echo "  - $dir"
done
echo

# Options menu
echo -e "${CYAN}What would you like to do?${NC}"
echo "1. Create and run all servers as Docker containers"
echo "2. Stop all Docker containers"
echo "3. Remove all Docker containers"
echo "4. Show running Docker containers"
echo "5. Exit"
read -p "Choose an option [1-5]: " option

case $option in
    1)
        echo -e "${BLUE}Creating and running all servers as Docker containers...${NC}"
        
        # Create the Dockerfile in the parent directory
        create_dockerfile
        
        # Build the base image just once
        image_name="stardeception-base"
        echo -e "${BLUE}Building base Docker image...${NC}"
        
        # Build from parent directory
        docker build -t "$image_name" .
        build_result=$?
        
        if [ $build_result -ne 0 ]; then
            echo -e "${RED}✗ Failed to build Docker image${NC}"
            exit 1
        else
            echo -e "${GREEN}✓ Docker image built successfully${NC}"
        fi
        
        # Start each server as a Docker container
        success_count=0
        for dir in "${server_dirs[@]}"; do
            if create_run_container "$dir" "$image_name"; then
                ((success_count++))
            fi
        done
        
        echo -e "${GREEN}✓ Created and started $success_count out of ${#server_dirs[@]} Docker containers${NC}"
        ;;
    2)
        echo -e "${BLUE}Stopping all StarDeception Docker containers...${NC}"
        containers=$(docker ps -a --filter "name=stardeception-" -q)
        
        if [ -z "$containers" ]; then
            echo -e "${YELLOW}No StarDeception containers found.${NC}"
        else
            docker stop $containers
            echo -e "${GREEN}✓ All StarDeception containers have been stopped.${NC}"
        fi
        ;;
    3)
        echo -e "${BLUE}Removing all StarDeception Docker containers...${NC}"
        containers=$(docker ps -a --filter "name=stardeception-" -q)
        
        if [ -z "$containers" ]; then
            echo -e "${YELLOW}No StarDeception containers found.${NC}"
        else
            docker rm -f $containers
            echo -e "${GREEN}✓ All StarDeception containers have been removed.${NC}"
        fi
        ;;
    4)
        echo -e "${BLUE}Running StarDeception containers:${NC}"
        docker ps --filter "name=stardeception-"
        ;;
    5)
        echo -e "${YELLOW}Goodbye!${NC}"
        exit 0
        ;;
    *)
        echo -e "${RED}Invalid option!${NC}"
        exit 1
        ;;
esac

echo
echo -e "${BLUE}📋 Docker container management tips:${NC}"
echo "  • To view container logs: docker logs <container_name>"
echo "  • To enter a container: docker exec -it <container_name> bash"
echo "  • To stop a container: docker stop <container_name>"
echo "  • To remove a container: docker rm <container_name>"
echo "  • To list all images: docker images"
echo "  • To remove an image: docker rmi <image_id>"
