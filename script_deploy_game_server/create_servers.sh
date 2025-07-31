#!/bin/bash

echo "
 ██████╗██████╗  ██████╗ 
██╔════╝██╔══██╗██╔═══██╗
███████╗██║  ██║██║   ██║
╚════██║██║  ██║██║   ██║
██████╔╝██████╔╝╚██████╔╝
╚═════╝ ╚═════╝  ╚═════╝ 
"
echo "========== Game Server Creator =========="
echo

read -p "How many servers do you want to create? " count
read -p "Enter the gameserver identifier (2 digits, e.g., 12): " id
read -p "Enter the SDO IP address: " sdo_ip

# Quick validation
if ! [[ $id =~ ^[0-9]{2}$ ]]; then
  echo "Invalid identifier. It must be composed of 2 digits."
  exit 1
fi

port=7050

for ((i=1; i<=count; i++)); do
  folder="server$i"
  mkdir -p "$folder"
  
  # Server number format: 01, 02, ...
  num=$(printf "%02d" $i)

  cat > "$folder/server.ini" <<EOF
[server]
name="gameserver${id}${num}"
port=${port}
SDO="${sdo_ip}"
EOF

  # Copy the server files from src directory
  cp "src/StarDeception.dedicated_server.sh" "$folder/"
  cp "src/StarDeception.dedicated_server.x86_64" "$folder/"

  ((port++))
done

echo "$count servers created successfully."
