#!/bin/bash

# Script pour créer la structure de fichiers pour le projet.
# Version améliorée utilisant un tableau et une boucle.

# Liste de tous les fichiers à créer
files_to_create=(
    "src/Identity/Domain/Event/IdentityAttachedToAccount.php"
    "src/Identity/Domain/Event/IdentityCreated.php"
    "src/Identity/Domain/Exception/IdentityAlreadyExistsException.php"
    "src/Identity/Domain/Exception/IdentityException.php"
    "src/Identity/Domain/Model/UserIdentity.php"
    "src/Identity/Domain/Repository/UserIdentityRepositoryInterface.php"
    "src/Identity/Domain/Specification/IsUniqueIdentitySpecification.php"
    "src/Identity/Domain/ValueObject/EmailIdentity.php"
    "src/Identity/Domain/ValueObject/Identifier.php"
    "src/Identity/Domain/ValueObject/PhoneIdentity.php"
    "src/Identity/Application/Command/AttachIdentityToAccount.php"
    "src/Identity/Application/Command/AttachIdentityToAccountHandler.php"
    "src/Identity/Application/Query/IsIdentityAvailable.php"
    "src/Identity/Application/Query/IsIdentityAvailableHandler.php"
    "src/Identity/Application/Service/IdentifierResolver.php"
    "src/Identity/Application/Service/IdentifierResolverInterface.php"
    "src/Identity/Infrastructure/Persistence/Doctrine/Mapping/Identity.Model.UserIdentity.orm.xml"
    "src/Identity/Infrastructure/Persistence/Doctrine/Repository/DoctrineUserIdentityRepository.php"
    "src/Identity/Infrastructure/Persistence/Doctrine/Specification/DoctrineIsUniqueIdentitySpecificationAdapter.php"
    "src/Identity/Infrastructure/Persistence/Doctrine/Types/EmailIdentityType.php"
    "src/Identity/Infrastructure/Persistence/Doctrine/Types/PhoneIdentityType.php"
    "src/Identity/Infrastructure/Symfony/DependencyInjection/Configuration.php"
    "src/Identity/Infrastructure/Symfony/DependencyInjection/IdentityExtension.php"
    "src/Identity/Infrastructure/Symfony/Resources/config/services.php"
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
