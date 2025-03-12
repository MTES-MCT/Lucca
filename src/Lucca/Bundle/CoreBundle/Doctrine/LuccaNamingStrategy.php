<?php
declare(strict_types=1);

namespace Lucca\Bundle\CoreBundle\Doctrine;

use Doctrine\ORM\Mapping\NamingStrategy;

class LuccaNamingStrategy implements NamingStrategy
{
    /**
     * {@inheritDoc}
     */
    public function classToTableName($className): string
    {
        if (str_contains($className, '\\')) {
            return substr($className, strrpos($className, '\\') + 1);
        }

        return $className;
    }

    /**
     * Converts a property name to a column name using camelCase.
     */
    public function propertyToColumnName($propertyName, $className = null): string
    {
        // Returns the property name as it is, maintaining camelCase
        return $propertyName;
    }

    /**
     * Specifies the name of the foreign key column (default: 'id').
     */
    public function referenceColumnName(): string
    {
        return 'id';
    }

    /**
     * Converts a property name to the name of a join column using underscores with a suffix 'Id'.
     */
    public function joinColumnName($propertyName, string $className): string
    {
        // Convert the property name to snake_case and append '_id'
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $propertyName)) . '_id';
    }

    /**
     * Combines the names of source and target entities into a join table name using underscores.
     */
    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null): string
    {
        // Use the short name of the source and target entity, converted to snake_case
        $sourceName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', basename(str_replace('\\', '/', $sourceEntity))));
        $targetName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', basename(str_replace('\\', '/', $targetEntity))));

        return $sourceName . '_' . $targetName; // Join table in snake_case
    }

    /**
     * Converts the entity name and referenced column name into a join key column name using underscores.
     */
    public function joinKeyColumnName($entityName, $referencedColumnName = null): string
    {
        // Use the short name of the entity, converted to snake_case
        $entityShortName = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', basename(str_replace('\\', '/', $entityName))));

        return $entityShortName . ($referencedColumnName ? '_' . strtolower($referencedColumnName) : '_id'); // Join key in snake_case
    }


    /**
     * Converts embedded field names to column names, keeping camelCase.
     */
    public function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null): string
    {
        return $propertyName . ucfirst($embeddedColumnName); // Combines while maintaining camelCase
    }
}
