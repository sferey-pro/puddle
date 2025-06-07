<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Twig\Components;

use App\Module\CostManagement\Application\Command\AddCostContribution;
use App\Module\CostManagement\Application\DTO\AddContributionDTO;
use App\Module\CostManagement\UI\Form\AddContributionFormType;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

/**
 * Composant Live pour le formulaire d'ajout d'une contribution.
 */
#[AsLiveComponent]
class AddContributionForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(updateFromParent: true)]
    public string $costItemId;

    public AddContributionDTO $data;

    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
    }

    /**
     * Initialise le DTO avec l'ID du CostItem courant.
     */
    protected function instantiateForm(): FormInterface
    {
        $this->data = new AddContributionDTO();
        $this->data->costItemId = $this->costItemId;

        return $this->createForm(AddContributionFormType::class, $this->data);
    }

    #[LiveAction]
    public function save(): void
    {
        $this->submitForm();

        /** @var AddContributionDTO $dto */
        $data = $this->getFormInstance()->getData();

        $this->commandBus->dispatch(new AddCostContribution($data));

        // On réinitialise le formulaire pour un nouvel ajout
        $this->resetForm();
        $this->data->costItemId = $this->costItemId;
    }
}
