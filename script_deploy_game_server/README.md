# Game Server Deployment Scripts

This folder contains two Bash scripts to automate the creation and deletion of game servers.

## Available Scripts

### 📁 `create_servers.sh`
Script to automatically create multiple game servers with their configurations.

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

**Features:**
- Interactive deletion with confirmation
- Deletes all folders starting with "server"
- Protection against accidental deletions

## Prerequisites

### Execution Permissions
Before using the scripts, you must give them execution permissions:

```bash
chmod +x create_servers.sh delete_servers.sh
```

### Required Environment
- Bash (Linux/WSL)
- Read/write permissions in the current directory

## Important Notes

- ⚠️ **Warning**: The `delete_servers.sh` script deletes **ALL** folders starting with "server" in the current directory
- Ports are assigned sequentially starting from 7050
- The identifier must be exactly 2 digits
- The scripts include basic validations to avoid common errors

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