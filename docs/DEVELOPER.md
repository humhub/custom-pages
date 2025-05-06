# Developer

Since CustomPages version v1.0 external modules can provide own targets through the **CustomPagesService**.

## Default templates

- Default templates are stored in JSON files `/resources/templates/`.
- To update them from administration panel change module property `$allowUpdateDefaultTemplates` to `true`.
- After updating a default module export it to JSON file and then update it in the folder `/resources/templates/`.
- All default templates are refreshed on each enabling or updating of this module or other module which has default template for custom pages.
- To manual refreshing of all default templates run the command `yii custom-pages/refresh-default-templates`.

### Default templates for another module

- Use event `humhub\modules\custom_pages\modules\template\services\ImportService::EVENT_DEFAULT_TEMPLATES` and code in the file `Events.php`:
```php
public static function onCustomPagesImportServiceDefaultTemplates(\humhub\modules\custom_pages\modules\template\events\DefaultTemplateEvent $event)
{
    $event->addPath('@your-module-id/resources/custom-pages-templates');
}
```
- Code for the file `Module.php`:
```php
public function enable()
{
    parent::enable() && $this->importCustomPagesDefaultTemplates();
}

public function update()
{
    parent::update();
    $this->importCustomPagesDefaultTemplates();
}

private function importCustomPagesDefaultTemplates(): bool
{
    $importServiceClassName = '\humhub\modules\custom_pages\modules\template\services\ImportService';
    return !method_exists($importServiceClassName, 'importDefaultTemplates') ||
        $importServiceClassName::instance()->importDefaultTemplates();
}
```
