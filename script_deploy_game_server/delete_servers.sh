#!/bin/bash

read -p "Voulez-vous vraiment supprimer tous les dossiers server* ? (o/n) " confirm
if [[ "$confirm" =~ ^[Oo]$ ]]; then
  rm -rf server*
  echo "Tous les dossiers server* ont été supprimés."
else
  echo "Annulé."
fi
