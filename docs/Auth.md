# Contexte d'Authentification (`Auth`)

## 1. Rôle et Responsabilités

Le contexte `Auth` est le cœur de la gestion des utilisateurs et de la sécurité de l'application. Il est conçu selon les principes de l'Architecture Hexagonale et du DDD pour être autonome et découplé des autres parties du système.

Ses responsabilités principales sont :
- **Gérer l'identité des utilisateurs** : Inscription, vérification et gestion des informations de connexion.
- **Fournir des mécanismes d'authentification sécurisés** : Gérer la manière dont les utilisateurs se connectent et accèdent à l'application.
- **Protéger les ressources** en s'assurant que seuls les utilisateurs authentifiés et autorisés peuvent effectuer certaines actions.
- **Interagir avec le contexte `UserManagement`** pour créer un profil utilisateur après une inscription réussie.

## 2. Fonctionnalités Principales

Le contexte `Auth` offre plusieurs méthodes d'inscription et de connexion pour une expérience utilisateur flexible et moderne.

### 2.1. Inscription des Utilisateurs

Un nouvel utilisateur peut créer un compte de deux manières distinctes :

#### a. Inscription Standard par Email

L'utilisateur peut s'inscrire en fournissant une adresse email et un mot de passe.
- **Processus** :
    1. L'utilisateur remplit le formulaire d'inscription (`RegistrationForm`).
    2. Une commande `RegisterUser` est envoyée au Command Bus.
    3. Le `RegisterUserHandler` valide que l'email n'est pas déjà utilisé.
    4. Un nouvel agrégat `UserAccount` est créé avec un rôle `ROLE_USER`.
    5. Un événement de domaine `UserRegistered` est publié.
- **Post-Inscription** :
    - L'événement `UserRegistered` est intercepté par un `listener` (`CreateUserOnUserRegisteredHandler`) qui déclenche la création du profil utilisateur dans le contexte `UserManagement`.
    - Un email de vérification est envoyé à l'utilisateur (`WhenUserRegisteredThenSendUserConfirmationEmail`).

#### b. Inscription via un Fournisseur OAuth (Google & GitHub)

L'utilisateur peut s'inscrire en utilisant son compte Google ou GitHub.
- **Processus** :
    1. L'utilisateur clique sur "Se connecter avec Google" ou "Se connecter avec GitHub".
    2. Il est redirigé vers le fournisseur d'identité pour autoriser l'application.
    3. Après autorisation, le fournisseur redirige vers l'application. Les `GoogleAuthenticator` et `GithubAuthenticator` interceptent la réponse.
    4. Le système vérifie si un `UserAccount` existe pour cet email.
    5. Si ce n'est pas le cas, un nouveau `UserAccount` est créé et un `SocialLink` est associé. L'événement `UserRegistered` est également publié.

### 2.2. Authentification

#### a. Connexion par Lien Magique (Magic Link)

Ce mécanisme permet une connexion sécurisée et sans mot de passe.
- **Processus** :
    1. L'utilisateur saisit son adresse email dans le formulaire de connexion.
    2. Une commande `RequestLoginLink` est envoyée.
    3. Le `RequestLoginLinkHandler` trouve le `UserAccount` associé à l'email.
    4. Il génère un lien de connexion sécurisé et à usage unique via le `LoginLinkManager`.
    5. Un événement `LoginLinkGenerated` est publié, et une notification par email (`CustomLoginLinkNotification`) est envoyée à l'utilisateur avec le lien.
    6. L'utilisateur clique sur le lien, il est automatiquement authentifié et redirigé.

#### b. Connexion via un Fournisseur OAuth (Google & GitHub)

Si un utilisateur s'est déjà inscrit (ou connecté une première fois) via OAuth, les connexions suivantes sont fluides.
- **Processus** :
    1. L'utilisateur clique sur le bouton de connexion du fournisseur.
    2. L'authentificateur correspondant (`GoogleAuthenticator` ou `GithubAuthenticator`) gère le flux.
    3. Il récupère les informations de l'utilisateur depuis le fournisseur (ex: `GoogleUser`).
    4. Il recherche le `UserAccount` associé à l'email ou au `SocialLink` existant.
    5. L'utilisateur est authentifié avec succès et une session est créée.

### 2.3. Vérification de l'Adresse Email

Pour s'assurer que l'email fourni par l'utilisateur est valide lors d'une inscription standard.
- **Processus** :
    1. Après l'inscription, l'email de l'utilisateur est marqué comme non vérifié (`isVerified = false`).
    2. Un email contenant un lien de vérification signé est envoyé (`EmailVerifier::sendEmailConfirmation`).
    3. L'utilisateur clique sur le lien. Le `VerifyEmailController` gère la requête.
    4. Il valide la signature du lien et marque l'utilisateur comme vérifié (`UserAccount::verify()`).
    5. Un événement `UserVerified` est publié.

### 2.4. Déconnexion

- **Processus** :
    1. L'utilisateur clique sur le lien de déconnexion.
    2. La route `logout` est appelée, gérée par le pare-feu de sécurité de Symfony.
    3. Le `LogoutUserHandler` est invoqué pour invalider le token de sécurité.
    4. Un événement `UserLoggedOut` est publié pour permettre à d'autres parties du système de réagir si nécessaire.

## 3. Architecture et Concepts Clés (DDD & CQRS)

Le code est structuré en couches pour respecter les principes du Blueprint.

### 3.1. Couche Domaine

C'est là que réside la logique métier.
- **Agrégats** :
    - `UserAccount`: L'agrégat principal. Il représente le compte de l'utilisateur avec ses informations d'authentification (email, mot de passe, rôles, statut de vérification). C'est lui qui garantit les invariants (règles métier) comme l'impossibilité de se connecter à un compte non vérifié.
    - `LoginLink`: Représente un lien magique à usage unique. Il contient le sélecteur, le hash du vérificateur et une date d'expiration.
    - `SocialLink`: Associe un `UserAccount` à un identifiant de fournisseur OAuth (ex: ID utilisateur Google).
- **Value Objects** :
    - `Email`, `Password`, `Hash`, `IpAddress`, `Locale` : Ces objets garantissent que les données sont toujours dans un état valide. Par exemple, un objet `Email` ne peut pas être instancié avec une chaîne qui n'est pas une adresse email valide.
- **Événements de Domaine** :
    - `UserRegistered`: Publié lorsqu'un compte est créé.
    - `UserLoggedIn`: Publié après une connexion réussie.
    - `LoginLinkGenerated`: Publié lorsqu'un lien magique est créé.
    - `UserVerified`: Publié lorsque l'email d'un utilisateur est vérifié.

### 3.2. Couche Application

Cette couche orchestre les cas d'utilisation.
- **Commandes** : Chaque commande représente une intention de modifier l'état du système.
    - `RegisterUser(RegisterUserDTO)`: Inscrire un nouvel utilisateur.
    - `RequestLoginLink(Email)`: Demander un lien magique.
    - `VerifyLoginLink(Request)`: Valider un lien magique.
    - `CreateAssociatedUserAccount(UserId, Social)`: Associer un compte social.
- **Queries** : Chaque query représente une intention de lire des données.
    - `FindUserByIdentifierQuery(string)`: Récupérer un utilisateur pour l'authentification.

### 3.3. Couche Infrastructure

Contient les implémentations techniques des interfaces définies dans le domaine.
- **Persistance** :
    - `DoctrineUserAccountRepository`: Implémentation du repository pour `UserAccount` avec Doctrine ORM (PostgreSQL).
- **Sécurité Symfony** :
    - `GoogleAuthenticator`, `GithubAuthenticator`: Gèrent les flux d'authentification OAuth2.
    - `AuthenticationLoginLinkSuccessHandler`, `AuthenticationLoginLinkFailureHandler`: Gèrent les succès et échecs de connexion par lien magique.
- **Services Externes** :
    - `LoginLinkGenerator`: Service qui s'appuie sur le `LoginLinkHelper` de Symfony pour créer des liens sécurisés.

### 3.4. Couche UI (User Interface)

Expose les fonctionnalités aux utilisateurs via des contrôleurs et des formulaires.
- `SecurityController`: Gère l'affichage des formulaires d'inscription et de connexion.
- `OAuthConnectController`: Gère le démarrage du flux de connexion OAuth.
- `VerifyEmailController`: Gère la validation du lien de vérification d'email.
- **Composants Twig** :
    - `RegistrationForm`: Composant réutilisable pour le formulaire d'inscription, respectant le Blueprint.

---
