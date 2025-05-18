# User

L'entité `User` est un élément central de notre domaine, représentant un utilisateur du système. 

Dans le contexte `Admin/User`, elle encapsule les informations nécessaires à la gestion des utilisateurs (création, modification, suppression, etc.). 

Simultanément, au sein du contexte `Auth`, l'`User` sert de point d'ancrage pour l'authentification et la gestion des informations d'identification, assurant ainsi l'accès sécurisé à l'application. 

Son identité unique (`UserId`) permet de le référencer et de le faire évoluer dans ces deux contextes distincts, chacun avec ses propres responsabilités et son propre modèle.

Scénario Enregistrement (Auth -> User -> Auth) :

[Auth] -> (Action d'enregistrement)
[Auth] -> Génère UserId
[Auth] -> Émet UserRegistered (avec UserId)
[User] <- Écoute UserRegistered
[User] -> Crée l'agrégat User (avec UserId)
[User] -> Émet UserCreated
[Auth] <- Écoute UserCreated
[Auth] -> Démarre le workflow d'enregistrement (mot de passe, email...)

Scénario Création User (User -> Auth) :

[User] -> (Action de création admin)
[User] -> Génère UserId
[User] -> Envoie Commande CreateUser (avec UserId)
[User] <- Reçoit Commande CreateUser
[User] -> Crée l'agrégat User (avec UserId)
[User] -> Émet UserCreated
[Auth] <- Écoute UserCreated
[Auth] -> Démarre le workflow d'enregistrement (mot de passe, email...)
