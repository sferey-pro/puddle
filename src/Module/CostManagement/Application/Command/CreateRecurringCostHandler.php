<?php

declare(strict_types=1);

namespace App\Module\CostManagement\Application\Command;

use App\Module\CostManagement\Domain\CostItem;
use App\Module\CostManagement\Domain\Enum\CostItemType;
use App\Module\CostManagement\Domain\Enum\RecurrenceFrequency;
use App\Module\CostManagement\Domain\RecurringCost;
use App\Module\CostManagement\Domain\Repository\CostItemRepositoryInterface;
use App\Module\CostManagement\Domain\Repository\RecurringCostRepositoryInterface;
use App\Module\CostManagement\Domain\ValueObject\CostItemName;
use App\Module\CostManagement\Domain\ValueObject\RecurrenceRule;
use App\Module\SharedContext\Domain\ValueObject\Money;
use App\Shared\Domain\Service\ClockInterface;
use App\Shared\Infrastructure\Symfony\Messenger\Attribute\AsCommandHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommandHandler]
final class CreateRecurringCostHandler
{
    public function __construct(
        private readonly CostItemRepositoryInterface $costItemRepository,
        private readonly RecurringCostRepositoryInterface $recurringCostRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ClockInterface $clock,
    ) {
    }

    public function __invoke(CreateRecurringCost $command): void
    {
        $dto = $command->dto;
        $templateDto = $dto->template; // On récupère le DTO imbriqué

        $this->entityManager->beginTransaction();
        try {
            // Étape 1 : Créer le CostItem modèle à partir du DTO imbriqué
            $costItemTemplate = CostItem::create(
                name: new CostItemName($templateDto->name),
                isTemplate: true,
                type: CostItemType::from($templateDto->type),
                targetAmount: new Money($templateDto->targetAmount, $templateDto->currency),
                coveragePeriod: null,
                description: $templateDto->description,
            );

            $this->costItemRepository->save($costItemTemplate);

            // Étape 2 : Créer la RecurrenceRule à partir du DTO principal
            $recurrenceRule = match (RecurrenceFrequency::from($dto->frequency)) {
                RecurrenceFrequency::DAILY => RecurrenceRule::daily(),
                RecurrenceFrequency::WEEKLY => RecurrenceRule::weeklyOn($dto->day),
                RecurrenceFrequency::MONTHLY => RecurrenceRule::monthlyOn($dto->day),
            };

            // Étape 3 : Créer le RecurringCost
            $recurringCost = RecurringCost::create(
                templateCostItemId: $costItemTemplate->id(),
                recurrenceRule: $recurrenceRule,
                clock: $this->clock
            );

            $this->recurringCostRepository->save($recurringCost);

            $this->entityManager->flush();
            $this->entityManager->commit();

            // Dispatch les événements de domaine
            foreach (array_merge($recurringCost->pullDomainEvents(), $costItemTemplate->pullDomainEvents()) as $domainEvent) {
                $this->eventDispatcher->dispatch($domainEvent);
            }
        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
}
