# StarDeception Game Server Manager - Quick Start Guide

## 🚀 Getting Started

### Step 1: Set Permissions (Linux/WSL/macOS)
```bash
chmod +x *.sh scripts/*.sh
```

### Step 2: Configure Download URL
Edit `src/StarDeception.dedicated_server_link.txt` and replace:
```
https://????????????
```
with your actual download URL (Google Drive, Dropbox, etc.)

### Step 3: Run the Main Script
```bash
./StarDeception_GameServer.sh
```

## 📋 Main Menu Options

```
========== StarDeception Game Server Manager ==========

Please select an option:

1) Create new servers    - Set up new game server instances
2) Delete all servers    - Remove all server directories  
3) Start all servers     - Launch all configured servers
4) Stop all servers      - Stop all running servers
5) Exit                  - Close the application
```

## 🔄 Typical Workflow

### First Time Setup
1. Run `./StarDeception_GameServer.sh`
2. Choose option **1** (Create new servers)
3. Enter:
   - Number of servers (e.g., 3)
   - 2-digit identifier (e.g., 01)
   - SDO IP address

### Starting Servers
1. Choose option **3** (Start all servers)
2. Script will automatically:
   - Check for dedicated server binary
   - Download if missing (prompts for URL if needed)
   - Start all configured servers in background

### Managing Running Servers
- **Stop all**: Choose option **4**
- **Check status**: `ps aux | grep StarDeception`
- **View logs**: Check `server.log` in each server directory

### Cleanup
- **Remove all servers**: Choose option **2**

## 🛠️ Manual Commands

If you prefer command line:

```bash
# Create servers
./scripts/create_servers.sh

# Start all servers
./scripts/start_all_servers.sh

# Stop all servers
./scripts/stop_all_servers.sh

# Delete all servers
./scripts/delete_servers.sh
```

## 📁 Directory Structure After Setup

```
script_deploy_game_server/
├── StarDeception_GameServer.sh     # Main menu script
├── scripts/                        # Secondary scripts directory
│   ├── create_servers.sh           # Server creation
│   ├── delete_servers.sh           # Server cleanup  
│   ├── start_all_servers.sh        # Start all servers
│   └── stop_all_servers.sh         # Stop all servers
├── src/
│   ├── StarDeception.dedicated_server.x86_64  # Downloaded binary
│   └── StarDeception.dedicated_server_link.txt # Download URL
├── server1/                        # Created servers
│   ├── StarDeception.dedicated_server.sh
│   ├── server.ini
│   └── server.log
├── server2/
└── server3/
```

## 🔧 Troubleshooting

### Binary Download Issues
- Ensure URL in `src/StarDeception.dedicated_server_link.txt` is correct
- Check you have `wget` or `curl` installed
- Verify internet connection

### Permission Issues
- Run: `chmod +x *.sh`
- Ensure you have write permissions in directory

### Servers Won't Start
- Check `server.log` files for error messages
- Verify binary exists: `ls -la src/StarDeception.dedicated_server.x86_64`
- Ensure binary is executable: `chmod +x src/StarDeception.dedicated_server.x86_64`

### Port Conflicts
- Default ports start at 7050
- Check if ports are already in use: `netstat -ln | grep 7050`

## 💡 Tips

- Servers run in background with output logged to `server.log`
- Each server gets a unique port (7050, 7051, 7052, etc.)
- Server names follow pattern: `gameserver[ID][number]` (e.g., gameserver0101)
- Binary is automatically downloaded only when needed
- Use the main menu for guided operations
