# Scripts de Déploiement de Serveurs de Jeu

Ce dossier contient deux scripts Bash pour automatiser la création et la suppression de serveurs de jeu.

## Scripts disponibles

### 📁 `create_servers.sh`
Script pour créer automatiquement plusieurs serveurs de jeu avec leurs configurations.

**Fonctionnalités :**
- Création interactive de multiples serveurs
- Configuration automatique des ports (à partir de 7050)
- Génération de fichiers `server.ini` avec les paramètres appropriés
- Nommage automatique des serveurs (gameserver + ID + numéro)

**Utilisation :**
Le script vous demandera :
- Le nombre de serveurs à créer
- Un identifiant à 2 chiffres (ex: 04)
- L'adresse IP du SDO (qui est confidentielle)

### 🗑️ `delete_servers.sh`
Script pour supprimer tous les dossiers de serveurs créés.

**Fonctionnalités :**
- Suppression interactive avec confirmation
- Supprime tous les dossiers commençant par "server"
- Protection contre les suppressions accidentelles

## Prérequis

### Permissions d'exécution
Avant d'utiliser les scripts, vous devez leur donner les permissions d'exécution :

```bash
chmod +x create_servers.sh delete_servers.sh
```

### Environnement requis
- Bash (Linux/WSL)
- Permissions de lecture/écriture dans le répertoire courant

## Notes importantes

- ⚠️ **Attention** : Le script `delete_servers.sh` supprime **TOUS** les dossiers commençant par "server" dans le répertoire courant
- Les ports sont attribués séquentiellement à partir de 7050
- L'identifiant doit être exactement 2 chiffres
- Les scripts incluent des validations de base pour éviter les erreurs communes

## Dépannage

### Erreur de permissions
Si vous obtenez une erreur "Permission denied" :
```bash
chmod +x create_servers.sh delete_servers.sh
```

### Erreur d'identifiant
Si l'identifiant n'est pas valide :
- Assurez-vous qu'il contient exactement 2 chiffres (ex: 01, 12, 99)
- Les lettres et caractères spéciaux ne sont pas autorisés