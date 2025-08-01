# Système d'Authentification Passwordless - Spécifications

## Contexte
Je développe un système d'authentification passwordless avec les caractéristiques suivantes :

### Stack Technique
- **Langage** : PHP 8.3+
- **Framework** : Symfony 7+
- **Composants** : Symfony LoginLink (décoré), Symfony Notifier
- **Architecture** : DDD avec 3 Bounded Contexts

### Vue d'Ensemble
- **Point d'entrée unique** : `puddle.com/start` (pas de distinction inscription/connexion)
- **Identifiants acceptés** : Email ou numéro de téléphone
- **Méthode d'authentification** : MagicLink (email) ou OTP (SMS)
- **Détection automatique** : Le système détermine si c'est une inscription ou connexion

## Décisions d'Architecture

### 1. Gestion des Identifiants
- **Identifiant Principal Unique** : UN SEUL identifiant peut servir à la connexion (PRIMARY_LOGIN)
- **Identifiants Secondaires** : Multiples identifiants possibles mais uniquement pour notifications
- **Changement possible** : L'utilisateur peut promouvoir un identifiant vérifié en principal
- **Vérification obligatoire** : Tout identifiant doit être vérifié avant utilisation

### 2. Bounded Contexts
- **Identity Context** : Gère les identifiants et leur statut
- **Authentication Context** : Gère les tokens, sessions et vérifications
- **Account/Registration Context** : Gère la création de compte et le profil utilisateur

### 3. Flow Unifié
1. Utilisateur saisit email/téléphone sur `/start`
2. Système vérifie si c'est un identifiant PRIMARY_LOGIN existant
3. Si OUI → Génère token de connexion
4. Si NON et compte existe → Message "Utilisez votre identifiant principal"
5. Si compte n'existe pas → Création compte + cet identifiant devient PRIMARY_LOGIN

### 4. Sécurité - Rate Limiting Progressif
**Par identifiant** avec escalade progressive :
- Tentatives 1-2 : Réponse immédiate
- Tentative 3 : Réponse immédiate + notification warning à l'utilisateur
- Tentative 4 : Délai 30 secondes + notification warning
- Tentative 5 : Délai 60 secondes + notification warning
- Tentative 6+ : Blocage 10 minutes + notification alerte

**Points clés** :
- Rate limiting sur le COMPTE (pas l'IP seule)
- Si compte introuvable, rate limiting sur l'identifiant
- Blocage manuel possible pour 24h en cas d'attaque
- Déblocage manuel possible par admin

### 5. Tokens et Persistence
- **Usage unique** : Chaque token/OTP utilisable une seule fois
- **Stockage obligatoire** : Tous les tokens persistés pour audit
- **TTL configurable** : Durée de vie en configuration YAML
- **Décorateur LoginLink** : Intercepte et persiste les tokens Symfony

### 6. Notifications
- **Email** → MagicLink envoyé
- **Téléphone** → Code OTP envoyé
- **Choix du canal** : Géré par Symfony Notifier selon le type d'identifiant
- **Types de notifications** :
  - Connexion/Inscription normale
  - Warning sécurité (tentatives multiples)
  - Alerte blocage

### 7. États et Validations
**États d'un identifiant** :
- `PENDING` : En attente de vérification
- `VERIFIED` : Vérifié et utilisable
- `REVOKED` : Révoqué (soft delete)

**États d'un compte** :
- `ACTIVE` : Peut se connecter
- `BLOCKED` : Bloqué temporairement (rate limit)
- `SUSPENDED` : Suspendu manuellement

### 8. Protection contre les Attaques
- **Timing constant** : 500ms minimum par requête (anti-enumeration)
- **Messages génériques** : Toujours "Vérifiez vos emails/SMS" (pas d'info sur existence)
- **Audit complet** : Toutes tentatives loggées avec IP, timestamp, résultat
- **Monitoring** : Alertes sur patterns suspects

## Contraintes et Règles Métier

1. **Unicité** : Un identifiant vérifié ne peut appartenir qu'à un seul compte
2. **Login unique** : Impossible de se connecter avec un identifiant NOTIFICATION
3. **Vérification première connexion** : L'utilisateur est informé si c'est sa première connexion
4. **Pas de suppression** : Les identifiants sont révoqués, jamais supprimés (audit)
5. **Migration future** : Architecture prête pour OAuth et password fallback

## Évolutions Prévues (non implémentées)
- Connexion OAuth (Google, Facebook, etc.)
- Fallback password pour utilisateurs existants
- Multi-dispositifs avec gestion des sessions
- 2FA/MFA optionnel

## Points d'Attention
- Le système accepte la fuite d'information minimale (on ne peut pas cacher totalement l'existence d'un compte)
- DoS possible mais mitigé par le rate limiting progressif
- Importance du monitoring pour détecter les abus
- Support client nécessaire pour déblocages manuels
