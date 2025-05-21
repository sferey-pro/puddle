<?php

declare(strict_types=1);

namespace App\Module\UserManagement\Application\Projector;

use App\Module\UserManagement\Application\ReadModel\Repository\UserViewRepositoryInterface as RepositoryUserViewRepositoryInterface;
use App\Module\UserManagement\Application\ReadModel\UserView;
use App\Module\UserManagement\Domain\Event\UserRegistered;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Projecteur pour mettre à jour le ReadModel UserView.
 *
 * Ce projecteur écoute les événements de domaine et met à jour la collection
 * MongoDB 'user_views' en conséquence.
 */
class UserViewProjector implements EventSubscriberInterface
{
    public function __construct(
        private readonly RepositoryUserViewRepositoryInterface $userViewRepository,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        // S'abonne à l'événement UserRegistered et appelle la méthode onUserRegistered
        return [
            UserRegistered::class => 'onUserRegistered',
        ];
    }

    public function onUserRegistered(UserRegistered $event): void
    {
        // Idéalement, vérifiez l'idempotence : si la vue existe déjà, ne la recréez pas
        // ou mettez-la à jour si nécessaire. Pour une création simple :
        $existingView = $this->userViewRepository->findById($event->getUserId());
        if ($existingView) {
            // Gérer le cas où la vue existe déjà (par exemple, logguer ou ignorer)
            // Pour cet exemple, nous allons simplement retourner pour éviter une erreur de duplication.
            return;
        }

        $userView = new UserView(
            userId: $event->getUserId()->value(), // Assumant que UserView attend un string pour userId
            email: $event->getEmail(),
            username: $event->getUsername(),
            isVerified: false // Par défaut, un nouvel utilisateur n'est pas vérifié
        );

        $this->userViewRepository->save($userView);
    }
}
