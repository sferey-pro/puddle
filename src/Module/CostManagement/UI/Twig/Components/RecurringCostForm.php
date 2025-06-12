<?php

declare(strict_types=1);

namespace App\Module\CostManagement\UI\Twig\Components;

use App\Module\CostManagement\Application\Command\CreateRecurringCost;
use App\Module\CostManagement\Application\DTO\CreateRecurringCostDTO;
use App\Module\CostManagement\Domain\Enum\RecurrenceFrequency;
use App\Module\CostManagement\UI\Form\RecurringCostFormType;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * LiveComponent pour le formulaire de création d'un coût récurrent.
 * Reçoit un DTO initial du contrôleur et gère l'interactivité du formulaire
 * (affichage dynamique des champs, soumission).
 */
#[AsLiveComponent()]
final class RecurringCostForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    /**
     * Reçoit le DTO initial (pré-rempli ou vide) depuis le contrôleur via le template.
     * C'est sur cette propriété que les champs du formulaire sont liés.
     */
    #[LiveProp()]
    public ?CreateRecurringCostDTO $data = null;

    /**
     * Action de sauvegarde, appelée par le bouton "Enregistrer" du formulaire.
     */
    #[LiveAction]
    public function save(CommandBusInterface $commandBus): RedirectResponse
    {
        // Valide et soumet les données du formulaire au DTO
        $this->submitForm();

        // Dispatche la commande CQRS avec le DTO rempli
        $commandBus->dispatch(new CreateRecurringCost($this->data));

        $this->addFlash('success', 'La planification du coût récurrent a été enregistrée avec succès.');

        return $this->redirectToRoute('recurring_cost_index');
    }

    /**
     * Méthode "helper" pour le template Twig, qui détermine si le champ "jour" doit être affiché.
     */
    public function needsDayField(): bool
    {
        return isset($this->data->frequency) &&
            in_array($this->data->frequency, [RecurrenceFrequency::WEEKLY, RecurrenceFrequency::MONTHLY], true);
    }

    /**
     * Crée l'instance du formulaire Symfony que le composant va utiliser.
     */
    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(RecurringCostFormType::class, $this->data);
    }
}
