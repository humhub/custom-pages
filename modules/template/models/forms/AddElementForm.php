<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\models\forms;

use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
use humhub\modules\custom_pages\modules\template\models\TemplateElement;
use ReflectionClass;
use Yii;
use yii\web\BadRequestHttpException;

/**
 * Form model used to add new TemplateElement instances to a Template.
 *
 * @author buddha
 */
class AddElementForm extends TemplateElementForm
{
    /**
     * Owner TemplateId of the new element definition.
     *
     * @var int
     */
    public $templateId;

    /**
     * Content type definition for this element.
     *
     * @var string
     */
    public $type;

    /**
     * Default scenario
     * @var type
     */
    public $scenario = 'create';

    /**
     * @inheritdocs
     */
    public function init()
    {
        $this->element = new TemplateElement(['scenario' => 'create']);
    }

    /**
     * @inheritdocs
     */
    public function rules()
    {
        return [
            [['templateId', 'type'], 'required'],
        ];
    }

    /**
     * Initializes the form data.
     *
     * Todo: rename because of definition...
     *
     * @param int $templateId
     * @param string $type
     */
    public function setElementDefinition($templateId, $type)
    {
        if (!is_string($type) || !is_subclass_of($type, BaseElementContent::class) || (new ReflectionClass($type))->isAbstract()) {
            throw new BadRequestHttpException('Invalid element type!');
        }

        $this->templateId = $templateId;
        $this->type = $type;

        $this->content = Yii::createObject($type);
        $this->element->content_type = $type;
        $this->element->template_id = $templateId;
    }

    /**
     * Validates and saves the TemplateElement instance and appended files.
     *
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            return $this->element->save(false) && $this->element->saveAsDefaultContent($this->content);
        } else {
            return false;
        }
    }

}
