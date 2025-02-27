# SettingBundle

## Installation

### Create settings and categories

#### Create a category :
If the category you need doesn't exist : 

Create it in the file SettingBundle/Utils/Generator/Data.php don't forget to add the categories, because without it the settings will not be created correctly.

Add an item like following in the tab categories
```php
0 => array(
            'name' => "setting.category.general.name",
            'icon' => "fas fa-wrench",
            'position' => 0,
            'comment' => "setting.category.general.comment"),
```
**Name** : "setting.category.categoryName.name" modify the categoryName, keep setting, category and name like it to have an ordered translations file.

**Icon** : Choose the icon on the website https://fontawesome.com/

**Position** : Store position, not used for now

**Comment** :  Take the name of the category and just change name to comment "setting.category.categoryName.comment"

#### Create a setting :
You need to create all the setting you need in the file SettingBundle/Utils/Generator/Data.php
To create a setting add an item in the array $settings, example of setting :
```php
0 => array(
            'name' => "setting.general.colorL.name",
            'type' => Setting::TYPE_COLOR,
            'category' => 'setting.category.general.name',
            'accessType' => Setting::ACCESS_TYPE_ADMIN,
            'position' => 0,
            'value' => '#23b7e5',
            'valuesAvailable' => [],
            'comment' => 'setting.general.colorL.comment'),
```
**Name** : "setting.categoryName.parameterName.name" modify the categoryName and parameterName, keep setting and name like it to have an ordered translations file.

**Type** : Available types are :
- TYPE_INTEGER
- TYPE_FLOAT
- TYPE_PERCENT
- TYPE_TEXT
- TYPE_BOOL
- TYPE_LIST
- TYPE_COLOR

**Category** : "setting.category.categoryName.name" modify categoryName with the name of the category

**AccessType** : Available access type are : 
- ACCESS_TYPE_SUPER_ADMIN
- ACCESS_TYPE_ADMIN

Please be careful when choose the access type 

**Position** : Store position, not used for now

**Value** : The value of the setting, you can set a default value, this field will be the only one editable in the views

**ValuesAvailable** : If the type is TYPE_LIST write here the available values like : 
```php
'valuesAvailable' => ['66', '34', '12'],
```

If the type is not TYPE_LIST let it empty.

**Comment** : Take the name of the setting and just change name to comment "setting.categoryName.parameterName.comment"


### Translations
In SettingBundle/Resources/translations/*Project*SettingBundle.fr.yml

Add all the translations you need for the settings and categories you create before under this comment :
```yml
###############
## Setting
## Here you need to add all the translations of your settings
###############
```

### Twig / Html and use / extends
You will need to change all the path in the different files of the bundle to match you project configuration (use and extends).
Also you need to modify the views to match the theme that is in place in your project.

## Use setting

### Controller
In a controller to use the settings you need to add the following line at the top of the php file :
```php
use Project\SettingBundle\Utils\SettingManager;
```

You can now call the manger in function like it : 
```php
$my_var = SettingManager::get('setting.categoryName.parameterName.name');
```

### View
In a twig view you can use the setting extension to get your setting :
```
{% 'setting.categoryName.parameterName.name'| setting %}
```
