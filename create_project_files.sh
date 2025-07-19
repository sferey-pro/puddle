#!/bin/bash

# Script pour créer la structure de fichiers pour le projet.
# Version améliorée utilisant un tableau et une boucle.

# Liste de tous les fichiers à créer
files_to_create=(
    "src/Authentication/Domain/AccessCredential/AccessCredentialInterface.php"
    "src/Authentication/Domain/AccessCredential/MagicLinkCredential.php"
    "src/Authentication/Domain/AccessCredential/OTPCredential.php"
    "src/Authentication/Domain/AccessCredential/PasswordCredential.php"
    "src/Authentication/Domain/AccessCredential/OAuthCredential.php"
    "src/Authentication/Domain/ValueObject/MagicLinkToken.php"
    "src/Authentication/Domain/ValueObject/OTPCode.php"
    "src/Authentication/Domain/ValueObject/CredentialType.php"
    "src/Authentication/Repository/AccessCredentialRepositoryInterface.php"
    "src/Authentication/Service/TokenGeneratorInterface.php"
    "src/Authentication/Application/Command/RequestMagicLink.php"
    "src/Authentication/Application/Command/RequestMagicLinkHandler.php"
    "src/Authentication/Application/Command/VerifyMagicLink.php"
    "src/Authentication/Application/Command/VerifyMagicLinkHandler.php"
    "src/Authentication/Application/Command/RequestOTP.php"
    "src/Authentication/Application/Command/RequestOTPHandler.php"
    "src/Authentication/Application/Command/VerifyOTP.php"
    "src/Authentication/Application/Command/VerifyOTPHandler.php"
    "src/Authentication/Application/Service/PasswordlessAuthenticationService.php"
    "src/Authentication/Infrastructure/Security/SymfonyLoginLinkAdapter.php"
    "src/Authentication/Infrastructure/Token/SymfonyTokenGenerator.php"
    "src/Authentication/Infrastructure/Notification/NotificationAdapter.php"
    "src/Kernel/Domain/NotificationInterface.php"
    "src/Kernel/Infrastructure/Notification/EmailNotificationAdapter.php"
    "src/Kernel/Infrastructure/Notification/SMSNotificationAdapter.php"
)

echo "Démarrage de la création de la structure..."
echo "---"

# Boucle sur chaque fichier de la liste
for file_path in "${files_to_create[@]}"; do
    # Extraire le chemin du répertoire parent du fichier
    dir_path=$(dirname "$file_path")

    # Créer le répertoire parent s'il n'existe pas (-p gère cela)
    mkdir -p "$dir_path"

    # Vérifier si le fichier existe
    if [ ! -f "$file_path" ]; then
        # S'il n'existe pas, le créer et afficher un message
        touch "$file_path"
        echo "Fichier créé : $file_path"
    else
        # Optionnel : message si le fichier existe déjà
        echo "Fichier existant : $file_path (ignoré)"
        : # L'opérateur ':' ne fait rien, il est utilisé comme placeholder
    fi
done

echo "---"
echo "Opération terminée."
