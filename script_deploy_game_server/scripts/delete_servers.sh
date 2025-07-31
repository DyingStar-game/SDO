#!/bin/bash

echo "
 ██████╗██████╗  ██████╗ 
██╔════╝██╔══██╗██╔═══██╗
███████╗██║  ██║██║   ██║
╚════██║██║  ██║██║   ██║
██████╔╝██████╔╝╚██████╔╝
╚═════╝ ╚═════╝  ╚═════╝ 
"
echo "========== Server Cleanup Tool =========="
echo

read -p "Do you really want to delete all server* folders? (y/n) " confirm
if [[ "$confirm" =~ ^[Yy]$ ]]; then
  rm -rf server*
  echo "All server* folders have been deleted."
else
  echo "Cancelled."
fi
