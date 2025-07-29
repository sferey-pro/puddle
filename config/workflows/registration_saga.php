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
        'compensated',
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

    # Étape 2 : Identity attachée
    $userRegistrationSaga->transition()
        ->name('attach_identity')
            ->from('account_created')
            ->to('identity_attached');

    # Étape 3 : User créé
    $userRegistrationSaga->transition()
        ->name('create_user')
            ->from('identity_attached')
            ->to('user_created');

    # Étape 4 : Email/SMS de bienvenue envoyé
    $userRegistrationSaga->transition()
        ->name('trigger_welcome')
            ->from('user_created')
            ->to('welcome_sent');

    # Étape 5 : Processus terminé
    $userRegistrationSaga->transition()
        ->name('complete')
            ->from('welcome_sent')
            ->to('completed');

    // ==================== TRANSITIONS DE COMPENSATION ====================

    # Compensation réussie depuis n'importe quel état non-final
    $userRegistrationSaga->transition()
        ->name('mark_as_compensated')
            ->from(['started', 'account_created', 'identity_attached', 'user_created', 'welcome_sent'])
            ->to('compensated');

    # Échec de compensation depuis n'importe quel état non-final
    $userRegistrationSaga->transition()
        ->name('mark_as_compensation_failed')
            ->from(['started', 'account_created', 'identity_attached', 'user_created', 'welcome_sent'])
            ->to('compensation_failed');
};
