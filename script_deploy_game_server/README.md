# StarDeception Game Server Management Scripts

This folder contains scripts to automate the creation, deletion, and startup of StarDeception game servers.

## 🚀 Quick Start

### Main Script: `StarDeception_GameServer.sh`
The main entry point with an interactive menu system.

**Usage:**
```bash
./StarDeception_GameServer.sh
```

This script provides a user-friendly menu with the following options:
1. **Create new servers** - Launch the server creation wizard
2. **Delete all servers** - Remove all server directories
3. **Start all servers** - Launch all configured servers (with automatic binary download)
4. **Exit** - Close the application

## 📋 Available Scripts

### 🎮 `StarDeception_GameServer.sh` *(NEW - Main Script)*
Interactive menu-driven script that orchestrates all server management operations.

**Features:**
- User-friendly colored interface
- Automatic dedicated server binary management
- Integrated download and setup process
- Menu-driven navigation with cancel options
- Input validation and error handling

### 📁 `create_servers.sh`
Script to automatically create multiple game servers with their configurations.

**Location**: `scripts/create_servers.sh`

**Features:**
- Interactive creation of multiple servers
- Automatic port configuration (starting from 7050)
- Automatic generation of `server.ini` files with appropriate parameters
- Automatic server naming (gameserver + ID + number)

**Usage:**
The script will ask you for:
- The number of servers to create
- A 2-digit identifier (e.g., 04)
- The SDO IP address (which is confidential)

### 🗑️ `delete_servers.sh`
Script to delete all created server folders.

**Location**: `scripts/delete_servers.sh`

**Features:**
- Interactive deletion with confirmation
- Deletes all folders starting with "server"
- Protection against accidental deletions

### ▶️ `start_all_servers.sh`
Script to start all configured game servers.

**Location**: `scripts/start_all_servers.sh`

**Features:**
- Automatic detection of server directories
- Dedicated server binary validation
- Colored output for better readability
- PID tracking for started servers
- Background process management

### ⏹️ `stop_all_servers.sh`
Script to stop all running game servers.

**Location**: `scripts/stop_all_servers.sh`

**Features:**
- Detection of running StarDeception processes
- Graceful shutdown with confirmation
- Colored output and process tracking

## 📦 Dedicated Server Binary Management

The dedicated server binary (`StarDeception.dedicated_server.x86_64`) is managed automatically:

- **Location**: `src/StarDeception.dedicated_server_link.txt` contains the download URL
- **Auto-download**: The main script will automatically download the binary when needed
- **Validation**: Scripts check for binary existence before starting servers
- **Size optimization**: The large binary file is not stored in the repository

### Setting up the Download URL

1. Edit `src/StarDeception.dedicated_server_link.txt`
2. Replace `https://????????????` with your actual download URL
3. The script will automatically use this URL for downloads

## Prerequisites

### Execution Permissions
Before using the scripts, you must give them execution permissions:

```bash
chmod +x *.sh scripts/*.sh
```

### Required Environment
- Bash (Linux/WSL/macOS)
- `wget` or `curl` for downloading the server binary
- Read/write permissions in the current directory

### Required Tools
- `wget` or `curl` (for binary downloads)
- `find` (for server directory detection)
- `grep` and `sed` (for URL processing)

## 🎯 Workflow

### First Time Setup
1. Run `./StarDeception_GameServer.sh`
2. Select option 1 to create servers
3. Provide the required information (server count, ID, IP)

### Starting Servers
1. Run `./StarDeception_GameServer.sh`
2. Select option 3 to start all servers
3. The script will automatically:
   - Check for the dedicated server binary
   - Download it if missing (will prompt for URL if not configured)
   - Start all configured servers

### Managing Servers
- Use the main menu to navigate between operations
- Each operation provides clear feedback and confirmation prompts
- Cancel options are available throughout the process

## Important Notes

- ⚠️ **Warning**: The `delete_servers.sh` script deletes **ALL** folders starting with "server" in the current directory
- Ports are assigned sequentially starting from 7050
- The identifier must be exactly 2 digits
- The scripts include comprehensive validations to avoid common errors
- Server logs are saved in `server.log` files within each server directory
- To stop all servers: `pkill -f StarDeception.dedicated_server`

## Troubleshooting

### Permission Error
If you get a "Permission denied" error:
```bash
chmod +x create_servers.sh delete_servers.sh
```

### Identifier Error
If the identifier is not valid:
- Make sure it contains exactly 2 digits (e.g., 01, 12, 99)
- Letters and special characters are not allowed