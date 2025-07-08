<?php

declare(strict_types=1);

namespace App\Module\UserManagement\UI\Twig\Components;

use App\Core\Application\Command\CommandBusInterface;
use App\Core\Application\Query\QueryBusInterface;
use App\Core\Domain\Result;
use App\Module\SharedContext\Domain\ValueObject\EmailAddress;
use App\Module\SharedContext\Domain\ValueObject\UserId;
use App\Module\UserManagement\Application\Command\CreateUser;
use App\Module\UserManagement\Application\DTO\CreateUserDTO;
use App\Module\UserManagement\Application\DTO\UpdateUserDTO;
use App\Module\UserManagement\Application\Query\FindUserQuery;
use App\Module\UserManagement\Application\ReadModel\UserView;
use App\Module\UserManagement\UI\Form\UserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * UserForm LiveComponent
 * Gère la création et la mise à jour d'un utilisateur de manière interactive.
 */
#[AsLiveComponent(name: 'UserManagement:UserForm', template: '@UserManagement/components/UserForm.html.twig')]
final class UserForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    /**
     * Le DTO utilisé pour les données du formulaire.
     * Il sera soit un CreateUserDTO, soit un UpdateUserDTO.
     * La propriété 'writable' permet au formulaire de modifier cet objet.
     */
    #[LiveProp(writable: true)]
    public CreateUserDTO|UpdateUserDTO|null $data = null;

    /**
     * Propriété pour recevoir l'objet UserView complet (pattern "Parent Fetches").
     */
    #[LiveProp]
    public ?UserView $initialUser = null;

    /**
     * Propriété pour recevoir l'ID de l'utilisateur, fourni uniquement en mode mise à jour. (pattern "Self Fetching").
     */
    #[LiveProp]
    public ?string $userId = null;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    /**
     * 'mount' est l'équivalent du constructeur pour un composant.
     * Il initialise l'état du composant.
     */
    public function mount(?UserView $initialUser = null, ?string $userId = null): void
    {
        $this->initialUser = $initialUser;
        // L'ID de confiance est soit celui passé directement, soit celui de l'objet initial.
        $this->userId = $userId ?? $this->initialUser?->id;

        if ($this->data) {
            return;
        }

        if ($this->isCreationMode()) {
            $this->data = new CreateUserDTO();

            return;
        }

        if (!$this->initialUser) {
            $this->initialUser = $this->queryBus->ask(new FindUserQuery($this->userId));
        }
    }

    /**
     * Action déclenchée lors de la soumission du formulaire.
     */
    #[LiveAction]
    public function save(): ?RedirectResponse
    {
        $this->submitForm();

        if ($this->getForm()->isValid()) {
            /** @var CreateUserDTO|UpdateUserDTO $dto */
            $dto = $this->getForm()->getData();

            $result = EmailAddress::create($dto->email);

            if ($this->isCreationMode()) {
                $command = new CreateUser(
                    userId: UserId::fromString($this->userId),
                    email: $result->value(),
                );
            }

            $this->commandBus->dispatch($command);

            $this->addFlash('success', 'Poste de coût créé avec succès !');

            return $this->redirectToRoute('user_index');
        }

        return null;
    }

    /**
     * Crée et retourne l'instance du formulaire Symfony.
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(UserFormType::class, $this->data, [
            'is_creation' => $this->isCreationMode(),
        ]);
    }

    private function isCreationMode(): bool
    {
        return null === $this->userId;
    }
}
