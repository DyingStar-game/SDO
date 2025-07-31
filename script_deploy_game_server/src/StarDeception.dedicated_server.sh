#!/bin/bash
echo -ne '\033c\033]0;StarDeception\a'
base_path="$(dirname "$(realpath "$0")")"

# Vérifier si le fichier binaire existe
if [ ! -f "$base_path/StarDeception.dedicated_server.x86_64" ]; then
    echo "ERREUR: Le fichier StarDeception.dedicated_server.x86_64 n'est pas trouvé."
    echo ""
    echo "Ce fichier doit être téléchargé séparément car il est trop volumineux pour le repository Git."
    echo "Consultez le fichier StarDeception.dedicated_server_link.txt pour les instructions de téléchargement."
    echo ""
    echo "Chemin attendu: $base_path/StarDeception.dedicated_server.x86_64"
    exit 1
fi

# Vérifier si le fichier est exécutable
if [ ! -x "$base_path/StarDeception.dedicated_server.x86_64" ]; then
    echo "AVERTISSEMENT: Le fichier StarDeception.dedicated_server.x86_64 n'a pas les permissions d'exécution."
    echo "Exécutez: chmod +x '$base_path/StarDeception.dedicated_server.x86_64'"
    echo ""
fi

"$base_path/StarDeception.dedicated_server.x86_64" "$@"
