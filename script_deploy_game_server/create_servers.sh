#!/bin/bash

read -p "Combien de serveurs voulez-vous créer ? " count
read -p "Entrez l'identifiant (2 chiffres, ex: 12) : " id
read -p "Entrez l'adresse IP du SDO : " sdo_ip

# Validation rapide
if ! [[ $id =~ ^[0-9]{2}$ ]]; then
  echo "Identifiant invalide. Il doit être composé de 2 chiffres."
  exit 1
fi

port=7050

for ((i=1; i<=count; i++)); do
  folder="server$i"
  mkdir -p "$folder"
  
  # Format du numéro de serveur : 01, 02, ...
  num=$(printf "%02d" $i)

  cat > "$folder/server.ini" <<EOF
[server]
name="gameserver${id}${num}"
port=${port}
SDO="${sdo_ip}"
EOF

  ((port++))
done

echo "$count serveurs créés avec succès."
