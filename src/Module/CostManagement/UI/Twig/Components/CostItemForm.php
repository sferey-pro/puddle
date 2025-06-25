<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Twig\Components;

use App\Core\Application\Command\CommandBusInterface;
use App\Module\CostManagement\Application\Command\CreateCostItem;
use App\Module\CostManagement\Application\DTO\CreateCostItemDTO;
use App\Module\CostManagement\UI\Form\CostItemFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * LiveComponent pour le formulaire de création et d'édition d'un CostItem.
 * Il gère son propre état (via le DTO), la validation et la soumission
 * de manière interactive sans rechargement de page complet.
 */
#[AsLiveComponent]
final class CostItemForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp()]
    public ?CreateCostItemDTO $data;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        // On passe le DTO (formDto) qui sert de modèle de données au formulaire.
        return $this->createForm(CostItemFormType::class, $this->data);
    }

    #[LiveAction]
    public function save(): ?RedirectResponse
    {
        // Soumet le formulaire avec les données actuelles du composant.
        // La validation est automatiquement déclenchée ici.
        $this->submitForm();

        // Si le formulaire est valide, on procède à la sauvegarde.
        if ($this->getForm()->isValid()) {
            /** @var CreateCostItemDTO $dto */
            $dto = $this->getForm()->getData();

            $this->commandBus->dispatch(new CreateCostItem($dto));

            $this->addFlash('success', 'Poste de coût créé avec succès !');

            return $this->redirectToRoute('cost_item_index');
        }

        return null;
    }
}
