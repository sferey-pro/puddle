<?php

declare(strict_types=1);

use App\Module\Auth\Domain\Saga\Process\RegistrationSagaProcess;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $userRegistrationSaga = $framework->workflows()->workflows('registration_saga');

    $userRegistrationSaga
        ->type('state_machine')
        ->supports(RegistrationSagaProcess::class)
        ->initialMarking('started');

    $userRegistrationSaga->auditTrail()->enabled(true);
    $userRegistrationSaga->markingStore()
            ->type('method')
            ->property('currentState');

    $places = [
        'started',
        'user_account_created',
        'user_profile_created',
        'welcome_link_triggered',
        'completed',
        'compensated',
        'compensation_failed',
    ];

    foreach ($places as $place) {
        $userRegistrationSaga->place($place);
    }

    # Étape 1 : UserAccount (Auth) créé
    $userRegistrationSaga->transition()
        ->name('create_user_account')
            ->from('started')
            ->to('user_account_created');

    # Étape 2 : User (UserManagement) créé
    $userRegistrationSaga->transition()
        ->name('create_user_profile')
            ->from('user_account_created')
            ->to('user_profile_created');

    # Étape 3 : Email de bienvenue envoyé
    $userRegistrationSaga->transition()
        ->name('trigger_welcome_link')
            ->from('user_profile_created')
            ->to('welcome_link_triggered');

    # Étape 4 : Processus terminé
    $userRegistrationSaga->transition()
        ->name('complete')
            ->from('welcome_link_triggered')
            ->to('completed');

    # --- Transitions de gestion d'échec ---
    # Transition pour un échec avec compensation réussie
    $userRegistrationSaga->transition()
        ->name('mark_as_compensated')
            ->from(['started', 'user_account_created', 'user_profile_created', 'welcome_link_triggered'])
            ->to('compensated');

    # Transition pour un échec critique de la compensation
    $userRegistrationSaga->transition()
        ->name('mark_as_compensation_failed')
            ->from(['started', 'user_account_created', 'user_profile_created', 'welcome_link_triggered'])
            ->to('compensation_failed');
};
