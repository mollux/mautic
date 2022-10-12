<?php

declare(strict_types=1);

namespace Mautic\IntegrationsBundle\Entity;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;

class FieldChange
{
    private ?int $id = null;

    private ?string $integration = null;

    private ?int $objectId = null;

    private ?string $objectType = null;

    private ?\DateTime $modifiedAt = null;

    private ?string $columnName = null;

    private ?string $columnType = null;

    private ?string $columnValue = null;

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder
            ->setTable('sync_object_field_change_report')
            ->setCustomRepositoryClass(FieldChangeRepository::class)
            ->addIndex(['object_type', 'object_id', 'column_name'], 'object_composite_key')
            ->addIndex(['integration', 'object_type', 'object_id', 'column_name'], 'integration_object_composite_key')
            ->addIndex(['integration', 'object_type', 'modified_at'], 'integration_object_type_modification_composite_key');

        $builder->addId();

        $builder
            ->createField('integration', Type::STRING)
            ->build();

        $builder->addBigIntIdField('objectId', 'object_id', false);

        $builder
            ->createField('objectType', Type::STRING)
            ->columnName('object_type')
            ->build();

        $builder
            ->createField('modifiedAt', Type::DATETIME)
            ->columnName('modified_at')
            ->build();

        $builder
            ->createField('columnName', Type::STRING)
            ->columnName('column_name')
            ->build();

        $builder
            ->createField('columnType', Type::STRING)
            ->columnName('column_type')
            ->build();

        $builder
            ->createField('columnValue', Types::TEXT)
            ->columnName('column_value')
            ->build();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getIntegration(): string
    {
        return $this->integration;
    }

    /**
     * @param string $integration
     *
     * @return FieldChange
     */
    public function setIntegration($integration)
    {
        $this->integration = $integration;

        return $this;
    }

    public function setObjectId(int $id): self
    {
        $this->objectId = $id;

        return $this;
    }

    public function getObjectId(): int
    {
        return $this->objectId;
    }

    public function setObjectType(string $type): self
    {
        $this->objectType = $type;

        return $this;
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    public function setModifiedAt(\DateTime $time): self
    {
        $this->modifiedAt = $time;

        return $this;
    }

    public function getModifiedAt(): \DateTime
    {
        return $this->modifiedAt;
    }

    public function setColumnName(string $name): self
    {
        $this->columnName = $name;

        return $this;
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function setColumnType(string $type): self
    {
        $this->columnType = $type;

        return $this;
    }

    public function getColumnType(): string
    {
        return $this->columnType;
    }

    public function setColumnValue(string $value): self
    {
        $this->columnValue = $value;

        return $this;
    }

    public function getColumnValue(): string
    {
        return $this->columnValue;
    }
}
