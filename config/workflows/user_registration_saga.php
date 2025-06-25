<?php

declare(strict_types=1);

use App\Module\Auth\Domain\Saga\RegisterUserSagaProcess;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $userRegistrationSaga = $framework->workflows()->workflows('user_registration_saga');

    $userRegistrationSaga
        ->type('state_machine')
        ->supports(RegisterUserSagaProcess::class)
        ->initialMarking('pending');

    $userRegistrationSaga->auditTrail()->enabled(true);
    $userRegistrationSaga->markingStore()
            ->type('method')
            ->property('currentState');

    $places = [
        'started',
        'user_account_created',
        'user_profile_created',
        'completed',
        'failed',
        'compensating',
        'compensated',
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

    # Fin du processus
    $userRegistrationSaga->transition()
        ->name('complete')
            ->from('user_profile_created')
            ->to('completed');

    # Gestion des échecs
    $userRegistrationSaga->transition()
        ->name('fail')
            ->from(['user_account_created', 'user_profile_created'])
            ->to('failed');

    # Compensation
    $userRegistrationSaga->transition()
        ->name('start_compensation')
            ->from('failed')
            ->to('compensating');

    $userRegistrationSaga->transition()
        ->name('finish_compensation')
            ->from('compensating')
            ->to('compensated');
};
