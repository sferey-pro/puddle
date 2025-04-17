<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

<?= $use_statements; ?>

#[ORM\Entity(repositoryClass: <?= $repository_class_name ?>::class)]
#[ORM\Table(name: '`<?= Symfony\Bundle\MakerBundle\Str::singularCamelCaseToPluralCamelCase($table_name) ?>`')]
<?php if ($api_resource): ?>
#[ApiResource]
<?php endif ?>
<?php if ($broadcast): ?>
#[Broadcast]
<?php endif ?>
class <?= $class_name."\n" ?> extends AbstractEntity
{
    public function jsonSerialize(): array
    {
        return [];
    }
}
