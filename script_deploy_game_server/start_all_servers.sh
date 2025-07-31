#!/bin/bash

echo "
 ██████╗██████╗  ██████╗ 
██╔════╝██╔══██╗██╔═══██╗
███████╗██║  ██║██║   ██║
╚════██║██║  ██║██║   ██║
██████╔╝██████╔╝╚██████╔╝
╚═════╝ ╚═════╝  ╚═════╝ 
"
echo "========== Start All Game Servers =========="
echo

# Find all server directories
server_dirs=($(find . -maxdepth 1 -type d -name "server*" | sort))

if [ ${#server_dirs[@]} -eq 0 ]; then
    echo "No server directories found. Please run create_servers.sh first."
    exit 1
fi

echo "Found ${#server_dirs[@]} server(s) to start:"
for dir in "${server_dirs[@]}"; do
    echo "  - $dir"
done
echo

# Ask for confirmation
read -p "Do you want to start all servers? (y/N): " confirm
if [[ ! $confirm =~ ^[Yy]$ ]]; then
    echo "Operation cancelled."
    exit 0
fi

echo "Starting all servers..."
echo

# Start each server
for dir in "${server_dirs[@]}"; do
    if [ -f "$dir/StarDeception.dedicated_server.sh" ]; then
        echo "Starting server in $dir..."
        cd "$dir"
        chmod +x StarDeception.dedicated_server.sh
        nohup ./StarDeception.dedicated_server.sh > server.log 2>&1 &
        server_pid=$!
        echo "  Server started with PID: $server_pid"
        cd ..
    else
        echo "  Warning: StarDeception.dedicated_server.sh not found in $dir"
    fi
done

echo
echo "All servers have been started!"
echo "Check individual server.log files in each server directory for output."
echo "To stop all servers, you can use: pkill -f StarDeception.dedicated_server"
