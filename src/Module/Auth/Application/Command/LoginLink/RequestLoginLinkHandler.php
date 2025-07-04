<?php

declare(strict_types=1);

namespace App\Module\Auth\Application\Command\LoginLink;

use App\Core\Application\Event\EventBusInterface;
use App\Core\Application\Query\QueryBusInterface;
use App\Core\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use App\Module\Auth\Application\Query\FindUserByIdentifierQuery;
use App\Module\Auth\Domain\Repository\UserRepositoryInterface;
use App\Module\Auth\Domain\Service\LoginLinkManager;
use App\Module\Auth\Domain\UserAccount;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Gère le cas d'utilisation "demander un lien de connexion".
 *
 * Responsabilités métier :
 * 1. Identifie un utilisateur à partir de son email ou de son nom d'utilisateur.
 * 2. Pour des raisons de sécurité, ne révèle pas si un identifiant existe ou non.
 * 3. Initie la création d'un lien de connexion pour l'utilisateur trouvé.
 * 4. Enregistre la demande de lien.
 * 5. Notifie les autres parties du système qu'un lien a été généré.
 */
#[AsCommandHandler]
final readonly class RequestLoginLinkHandler
{
    public function __construct(
        private QueryBusInterface $queryBus,
        private UserRepositoryInterface $repository,
        private LoginLinkManager $loginLinkManager,
        private EventBusInterface $eventBus,
        private EntityManagerInterface $em,
    ) {
    }

    public function __invoke(RequestLoginLink $command): void
    {
        /** @var ?UserAccount $user */
        $user = $this->queryBus->ask(new FindUserByIdentifierQuery($command->identifier));

        // Pour des raisons de sécurité, aucune action n'est effectuée si l'identifiant n'est pas trouvé.
        if (null === $user) {
            return;
        }

        // Délègue la création du lien de connexion.
        $this->loginLinkManager->createForStandardLogin($user, $command->ipAddress);

        // Enregistre la demande de connexion.
        $this->repository->add($user);
        $this->em->flush();

        // Notifie les autres systèmes (ex: pour l'envoi d'email) qu'un lien a été généré.
        $this->eventBus->publish(...$user->pullDomainEvents());
    }
}
