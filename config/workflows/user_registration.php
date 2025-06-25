<?php

declare(strict_types=1);

use App\Shared\Saga\Domain\SagaState;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $userRegistration = $framework->workflows()->workflows('user_registration');

    $userRegistration
        ->type('state_machine')
        ->supports(SagaState::class)
        ->initialMarking('pending');

    $userRegistration->auditTrail()->enabled(true);
    $userRegistration->markingStore()
            ->type('method')
            ->property('status');

    // Définition des "places", qui sont les états possibles de notre Saga.
    $places = [
        'pending',
        'creating_account',
        'account_created',
        'creating_profile',
        'completed',
        'compensating_account_creation',
        'failed',
    ];

    foreach ($places as $place) {
        $userRegistration->place($place);
    }


    // Définition des "transitions", qui sont les actions permettant de changer d'état.
    $userRegistration->transition()
        ->name('start')
            ->from('pending')
            ->to('creating_account');

    $userRegistration->transition()
        ->name('account_created')
            ->from('creating_account')
            ->to('account_created');

    $userRegistration->transition()
        ->name('initiate_profile_creation')
            ->from('account_created')
            ->to('creating_profile');

    $userRegistration->transition()
        ->name('profile_created')
            ->from('creating_profile')
            ->to('completed');

    $userRegistration->transition()
        ->name('fail_account_creation')
            ->from('creating_account')
            ->to('failed');

    $userRegistration->transition()
        ->name('fail_profile_creation')
            ->from('creating_profile')
            ->to('compensating_account_creation');

    $userRegistration->transition()
        ->name('finish_account_compensation')
            ->from('compensating_account_creation')
            ->to('failed');

};
