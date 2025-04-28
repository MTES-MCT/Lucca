<?php

/*
 * Copyright (c) 2025. Numeric Wave
 *
 * Affero General Public License (AGPL) v3
 *
 * For more information, please refer to the LICENSE file at the root of the project.
 */

namespace Lucca\Bundle\SettingBundle\Generator;

use Exception;
use Doctrine\ORM\EntityManagerInterface;
use Lucca\Bundle\DepartmentBundle\Entity\Department;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\CacheInterface;

use Lucca\Bundle\SettingBundle\Entity\{Category, Setting};
use Lucca\Bundle\SettingBundle\Manager\SettingManager;

/**
 * Setting Generator stores settings in database if they do not exist yet.
 *
 * @see SettingManager to get settings value and customize project
 */
class SettingGenerator
{
    const SETTINGS_CACHE_KEY = 'lucca.settings';

    protected array $aDatabaseSettingDictionary = [];

    protected array $aSettingToUpdateCallbacks = [];

    /** Setting in their minimal shape */
    protected array $aOutputDictionary;

    protected array $settings;
    protected array $categories;

    public function __construct(
        protected readonly EntityManagerInterface $em,
//        protected readonly AdapterInterface       $cache,
        protected readonly DataGenerator          $dataGenerator,
        protected readonly CacheInterface         $settingsCache,
    )
    {
        $this->settings = $dataGenerator->settings;
        $this->categories = $dataGenerator->categories;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function clearCachedSettings(): void
    {
        $this->settingsCache->deleteItem(self::SETTINGS_CACHE_KEY);
    }

    /**
     * Most efficient way of updating a single setting directly in the cache.
     *
     * @throws InvalidArgumentException
     */
    public function updateCachedSetting(string $name, mixed $value, Department $department): void
    {
        $item = $this->settingsCache->getItem(self::SETTINGS_CACHE_KEY . '.' . $department->getId());

        if ($item->isHit()) {
            $aDictionary = $item->get();

            if (!array_key_exists($name, $aDictionary)) {
                $aDictionary[$name] = $value;
                $item->set($aDictionary);
                $this->settingsCache->save($item);

                return;
            }

            if ($aDictionary[$name] !== $value) {
                $aDictionary[$name] = $value;
                $item->set($aDictionary);
                $this->settingsCache->save($item);
            }
        }
    }

    /**
     * Get cached settings (and generate when missing).
     *
     * @param bool $bypassCache Bypassing the cache will regenerate settings.
     *
     * @throws InvalidArgumentException
     */
    public function getCachedSettings(?Department $department, bool $bypassCache = false): array
    {
        $item = $this->settingsCache->getItem(self::SETTINGS_CACHE_KEY . '.' . $department?->getCode());

        if (!$item->isHit() || $bypassCache) {
            /** Check if all parameters exist in database in order to create them */
            try {
                $aSettingDictionary = $this->generateMissingSettings($department);

                $item->set($aSettingDictionary);
                $this->settingsCache->save($item);

                $this->em->flush();
            } catch (Exception $e) {
                // An error occurred, do nothing
            }
        }

        // Get cached value
        $cachedItemValue = $item->get();

        // Always return an array
        return is_array($cachedItemValue) ? $cachedItemValue : [];
    }

    /**
     * Check if all setting are created in Database.
     *
     * @throws Exception
     */
    public function generateMissingSettings(?Department $department): array
    {
        $this->aOutputDictionary = []; // init output
        $this->aDatabaseSettingDictionary = [];

        /** Try to get setting from datatable */
        try {
            if ($this->em instanceof EntityManagerInterface) {

                $databaseResponse = $this->em->getRepository(Setting::class)->findAllOptimized($department);

                // Ensure the database response is an array.
                if (is_array($databaseResponse)) {
                    $aName = array_column($databaseResponse, 'name');
                    $aCastValue = array_map(function ($row) {
                        return Setting::castValue($row['type'], $row['value']);
                    }, $databaseResponse);

                    $this->aDatabaseSettingDictionary = array_combine($aName, $aCastValue);
                    unset($aName, $aCastValue);
                }
            }
        } catch (Exception $e) {
            // An error occurred, do nothing
        }

        // Check all category in the categories array
        foreach ($this->categories as $category) {
            $this->insertOrUpdateCategory($category['name'], $category['icon'], $category['position'], $category['comment']);
        }

        // Check all settings in the settings array
        foreach ($this->settings as $setting) {
            $this->insertOrUpdateSetting($setting['type'], $setting['category'], $setting['accessType'], $setting['position'],
                $setting['name'], $setting['value'], $setting['comment'], $department, $setting['valuesAvailable']
            );
        }

        // Update pre-existing settings based on a list of callback function.
        $this->updateSettings($department);

        $aOutputDictionary = $this->aOutputDictionary;
        $this->aOutputDictionary = []; // empty for future output

        return $aOutputDictionary;
    }

    /******************************************* Category **************************************/

    /**
     * Check if setting is already created -> if not insert it in database
     */
    private function insertOrUpdateCategory($name, $icon, $position, $comment): void
    {
        // Try to find the category
        $category = $this->em->getRepository(Category::class)->findOneBy(array(
            'name' => $name
        ));

        // if the category doesn't exist create it
        if (!$category) {
            $category = new Category();
            $category->setName($name);
            $category->setIcon($icon);
            $category->setPosition($position);
            $category->setComment($comment);

            $this->em->persist($category);
        }
    }

    /******************************************* Setting **************************************/

    /**
     * Check if setting is already created -> if not insert it in database
     *
     * @throws Exception
     */
    public function insertOrUpdateSetting($type, $_category, $accessType, $position, $name, $value, $description, $department, $values = null): bool
    {
        // Try to find the required category
        /** @var Category $category */
        $category = $this->em->getRepository(Category::class)->findOneBy(array(
            'name' => $_category
        ));

        // If the category doesnt exist print an error message
        if (!$category) {
            echo "Error when creating settings. Category " . $_category . " doesn't exist";

            return false;
        }

        if (!is_array($this->aDatabaseSettingDictionary) || array_key_exists($name, $this->aDatabaseSettingDictionary) === false) {
            // Insert the new Setting
            $setting = new Setting($name, $type, $category, $accessType, $position, $value, $description, $values);

            $this->em->persist($setting);
            $this->aOutputDictionary[$setting->getName()] = Setting::castValue($setting->getType(), $setting->getValue());
        } else {
            $this->aSettingToUpdateCallbacks[$name] = function (Setting $setting) use ($name, $type, $category, $accessType, $position, $value, $description, $values, $department) {
                /** Update only the values that don't have an impact on given value */
                $setting->setName($name);
                $setting->setType($type);
                $setting->setCategory($category);
                $setting->setAccessType($accessType);
                $setting->setPosition($position);
                $setting->setComment($description);
                $setting->setDepartment($department);
                if ($type === Setting::TYPE_LIST) {
                    $setting->setvaluesAvailable(implode(';', $values));
                }

                $this->em->persist($setting);
                $this->aOutputDictionary[$setting->getName()] = Setting::castValue($setting->getType(), $setting->getValue());
            };
        }

        return true;
    }

    /**
     * Update pre-existing settings based on a list of callback function.
     */
    private function updateSettings(?Department $department): void
    {
        if (is_array($this->aSettingToUpdateCallbacks) && $this->aSettingToUpdateCallbacks !== []) {
            // Find all settings to update
            $aSettingToUpdate = $this->em->getRepository(Setting::class)
                ->findBy(['department' => $department?->getId(), 'name' => array_keys($this->aSettingToUpdateCallbacks)]);

            // Index all settings by name
            $aSettingToUpdateIndexedByName = array_combine(array_map(function (Setting $setting) {
                return $setting->getName();
            }, $aSettingToUpdate), $aSettingToUpdate);

            /**
             * @var string $name
             * @var Setting $setting
             */
            foreach ($aSettingToUpdateIndexedByName as $name => $setting) {
                // Test callback existence, just to be extra safe
                if (isset($this->aSettingToUpdateCallbacks[$name])) {
                    // Call the callback function to update the setting found in database
                    $this->aSettingToUpdateCallbacks[$name]($setting);
                }
            }
            unset($name, $setting);
        }
    }
}
