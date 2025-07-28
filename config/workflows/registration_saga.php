<?php

declare(strict_types=1);

use Account\Registration\Domain\Saga\Process\RegistrationSagaProcess;
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
        'account_created',
        'identity_attached',
        'user_created',
        'welcome_sent',
        'completed',
        'failed',
        'compensation_failed',
    ];

    foreach ($places as $place) {
        $userRegistrationSaga->place($place);
    }

    # Étape 1 : Account créé
    $userRegistrationSaga->transition()
        ->name('create_account')
            ->from('started')
            ->to('account_created');

    # Étape 1 : Identity créé et attribué
    $userRegistrationSaga->transition()
        ->name('attach_identity')
            ->from('account_created')
            ->to('identity_attached');

    # Étape 2 : Account/Profile créé
    $userRegistrationSaga->transition()
        ->name('create_user')
            ->from('identity_attached')
            ->to('user_created');

    # Étape 3 : Email ou Sms de bienvenue envoyé
    $userRegistrationSaga->transition()
        ->name('trigger_welcome')
            ->from('user_created')
            ->to('welcome_sent');

    # Étape 4 : Processus terminé
    $userRegistrationSaga->transition()
        ->name('complete')
            ->from('welcome_sent')
            ->to('completed');

    # --- Transitions de gestion d'échec ---
    # Transition pour un échec avec compensation réussie
    $userRegistrationSaga->transition()
        ->name('mark_as_compensated')
            ->from(['started', 'account_created', 'identity_attached', 'profile_created', 'welcomed'])
            ->to('compensated');

    # Transition pour un échec critique de la compensation
    $userRegistrationSaga->transition()
        ->name('mark_as_compensation_failed')
            ->from(['started', 'account_created', 'identity_attached', 'profile_created', 'welcomed'])
            ->to('compensation_failed');
};
