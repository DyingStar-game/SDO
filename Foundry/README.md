# StarDeception Game Server Manager

Simple interactive script to manage StarDeception game servers.

## 🚀 Quick Start

### 1. Go in the right folder
```bash
cd ./SDO/Foundry
```

### 2. Run the Manager
```bash
sudo bash StarDeception_GameServer.sh
```

### 3. Follow the Menu
The script provides an interactive menu with these options:
- **Create new servers** - Set up game server instances
- **Delete all servers** - Remove all server directories
- **Start all servers** - Launch configured servers (auto-downloads binary)
- **Stop all servers** - Stop running servers
- **Exit** - Close the application

## � First Time Setup

1. Run the script: `./StarDeception_GameServer.sh`
2. Choose "Create new servers"
3. Enter the required information when prompted
4. Use "Start all servers" to launch them

The script will automatically handle the dedicated server binary download when needed.

## 📁 What Gets Created

```
Foundry/
├── StarDeception_GameServer.sh    # Main script (run this)
├── scripts/                       # Helper scripts (auto-managed)
├── src/                          # Server binary and config
└── server1/, server2/, ...       # Your game servers
```

## 🔧 Requirements

- Bash shell (Linux/WSL/macOS)
- `wget` or `curl` (for binary downloads)
- Write permissions in the directory

That's it! The script handles everything else for you.

### Permission Error
If you get a "Permission denied" error:
```bash
chmod +x create_servers.sh delete_servers.sh
```

### Identifier Error
If the identifier is not valid:
- Make sure it contains exactly 2 digits (e.g., 01, 12, 99)
- Letters and special characters are not allowed

---

## 👨‍💻 Contributeur

**Game Server Manager développé par [𝕭𝖗𝖚𝖒𝖊](https://noasecond.com)**
