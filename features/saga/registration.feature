Feature: Saga d'inscription utilisateur
  Pour garantir l'intégrité du système,
  l'inscription d'un nouvel utilisateur doit être un processus transactionnel complet
  qui réussit entièrement ou échoue proprement en annulant toutes ses actions.

  Background:
    Given le système est dans un état initial propre

  Scenario: Un utilisateur s'inscrit avec succès (Happy Path)
    When je tente de m'inscrire avec l'email "happy.user@example.com" et le nom d'utilisateur "happy_user"
    Then un "UserAccount" doit exister dans le module Auth avec l'email "happy.user@example.com"
    And un "User" doit exister dans le module UserManagement avec l'email "happy.user@example.com"
    And la saga d'inscription pour "happy.user@example.com" doit être marquée comme "completed"

  Scenario: L'inscription échoue pendant la création du profil et la compensation s'exécute
    Given la création du profil utilisateur pour "sad.user@example.com" est configurée pour échouer
    When je tente de m'inscrire avec l'email "sad.user@example.com" et le nom d'utilisateur "sad_user"
    Then un "UserAccount" ne doit pas exister dans le module Auth avec l'email "sad.user@example.com"
    And la saga d'inscription pour "sad.user@example.com" doit être marquée comme "failed"
