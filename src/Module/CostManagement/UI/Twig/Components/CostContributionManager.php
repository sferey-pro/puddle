<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Twig\Components;

use App\Module\CostManagement\Application\Command\AddCostContribution;
use App\Module\CostManagement\Application\Command\RemoveCostContribution;
use App\Module\CostManagement\Application\Command\UpdateCostContribution;
use App\Module\CostManagement\Application\DTO\AddContributionDTO;
use App\Module\CostManagement\Application\Query\FindCostItemQuery;
use App\Module\CostManagement\Application\ReadModel\ContributionView;
use App\Module\CostManagement\Application\ReadModel\CostItemView;
use App\Module\CostManagement\Domain\ValueObject\CostItemId;
use App\Module\CostManagement\UI\Form\ContributionItemFormType;
use App\Shared\Application\Command\CommandBusInterface;
use App\Shared\Application\Query\QueryBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * Ce composant gère l'affichage, l'ajout et l'édition en ligne des contributions.
 * Il utilise ComponentWithFormTrait pour gérer l'état du formulaire actif (soit l'ajout, soit l'édition).
 */
#[AsLiveComponent]
class CostContributionManager extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(updateFromParent: true)]
    public string $costItemId;

    /**
     * @var AddContributionDTO le DTO lié au formulaire actuellement affiché (ajout ou édition)
     */
    #[LiveProp(writable: true)]
    public AddContributionDTO $data;

    /**
     * @var string|null L'ID de la contribution en cours d'édition. Null si nous sommes en mode "ajout".
     */
    #[LiveProp(writable: true)]
    public ?string $editingId = null;

    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    /**
     * La méthode mount est appelée lors de l'initialisation du composant.
     * Nous préparons le DTO pour le formulaire d'ajout.
     */
    public function mount(): void
    {
        $this->data = new AddContributionDTO();
    }

    #[LiveAction]
    public function activateEditing(#[LiveArg] string $id): void
    {
        $this->editingId = $id;
    }

    /**
     * Instancie le formulaire et le lie à notre propriété $data.
     * C'est la méthode requise par ComponentWithFormTrait.
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ContributionItemFormType::class, $this->data);
    }

    /**
     * Fournit la vue du CostItem au template.
     */
    public function getCostItem(): CostItemView
    {
        return $this->queryBus->ask(new FindCostItemQuery(CostItemId::fromString($this->costItemId)));
    }

    /**
     * Action pour AJOUTER une nouvelle contribution.
     * Déclenchée par le bouton "Ajouter".
     */
    #[LiveAction]
    public function add(): void
    {
        $this->submitForm();

        /** @var AddContributionDTO $dto */
        $dto = $this->getForm()->getData();
        $dto->costItemId = $this->costItemId;

        $this->commandBus->dispatch(new AddCostContribution($dto));
        $this->resetState();
    }

    /**
     * Action pour METTRE A JOUR une contribution existante.
     * Déclenchée par le bouton "Sauvegarder" en mode édition.
     */
    #[LiveAction]
    public function update(): void
    {
        $this->submitForm();

        /** @var AddContributionDTO $dto */
        $dto = $this->getForm()->getData();
        $dto->costItemId = $this->costItemId;
        $dto->contributionId = $this->editingId;

        $this->commandBus->dispatch(new UpdateCostContribution($dto));
        $this->resetState();
    }

    /**
     * Action pour démarrer l'édition d'une contribution.
     */
    #[LiveAction]
    public function edit(#[LiveArg] string $contributionId): void
    {
        $this->editingId = $contributionId;

        $contributionToEdit = $this->findContributionViewById($contributionId);

        // On charge les données de la contribution à éditer dans le DTO du formulaire.
        $editingDto = new AddContributionDTO();
        if ($contributionToEdit) {
            $editingDto->amount = $contributionToEdit->amount;
            $editingDto->sourceProductId = $contributionToEdit->sourceProductId;
        }
        $this->data = $editingDto;
        $this->resetForm();
    }

    /**
     * Annule le mode édition.
     */
    #[LiveAction]
    public function cancelEdit(): void
    {
        $this->resetState();
    }

    /**
     * Supprime une contribution.
     */
    #[LiveAction]
    public function remove(#[LiveArg] string $contributionId): void
    {
        $this->commandBus->dispatch(new RemoveCostContribution($this->costItemId, $contributionId));

        // Si on supprime l'élément qu'on était en train d'éditer, on reset l'état.
        if ($this->editingId === $contributionId) {
            $this->resetState();
        }
    }

    /**
     * Réinitialise le composant à son état initial (formulaire d'ajout).
     */
    private function resetState(): void
    {
        $this->editingId = null;
        $this->data = new AddContributionDTO();
        $this->resetForm();
    }

    private function findContributionViewById(string $id): ?ContributionView
    {
        foreach ($this->getCostItem()->contributions as $contribution) {
            if ($contribution->id === $id) {
                return $contribution;
            }
        }

        return null;
    }
}
