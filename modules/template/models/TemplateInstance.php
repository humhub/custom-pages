<?php

namespace humhub\modules\custom_pages\modules\template\models;

use humhub\components\ActiveRecord;
use humhub\modules\content\models\Content;
use humhub\modules\custom_pages\models\CustomPage;
use Yii;
use yii\db\ActiveQuery;

/**
 * A TemplateInstance represents an actual instantiation of a Template model.
 * The TemplateInstance can be for example a Page or Snippet related by the PolymorphicRelation behaviour.
 *
 * @property int $id
 * @property int $page_id
 * @property int $template_id
 *
 * @property-read Template $template
 */
class TemplateInstance extends ActiveRecord implements TemplateContentOwner
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => \humhub\components\behaviors\PolymorphicRelation::class,
                'mustBeInstanceOf' => [ActiveRecord::class],
            ],
        ];
    }

    /**
     * @return string the associated database table name
     */
    public static function tableName()
    {
        return 'custom_pages_template_container';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template_id', 'page_id'], 'required'],
            [['template_id', 'page_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        foreach (OwnerContent::findByOwner($this)->all() as $content) {
            $content->delete();
        }
    }

    /**
     * Returns the default element of the element identified by $elementName of the given TemplateInstance identified by $id.
     *
     * @param \humhub\modules\custom_pages\modules\template\models\TemplateInstance|int $id
     * @param string $elementName
     * @return type
     */
    public static function getDefaultElement($id, $elementName)
    {
        $pageTemplate = ($id instanceof TemplateInstance) ? $id : self::find()->where(['custom_pages_page_template.id' => $id])->joinWith('template')->one();
        return $pageTemplate->template->getElement($elementName);
    }

    public static function findByTemplateId($templateId, ?int $contentState = null): ActiveQuery
    {
        $query = self::find()->where(['template_id' => $templateId]);

        if ($contentState !== null) {
            $query->leftJoin(
                Content::tableName(),
                Content::tableName() . '.object_model = :object_model AND ' .
                Content::tableName() . '.object_id = ' . self::tableName() . '.page_id',
                ['object_model' => CustomPage::class],
            )
                ->andWhere([Content::tableName() . '.state' => $contentState]);
        }

        return $query;
    }

    public function render($editMode = false)
    {
        return $this->template->render($this, $editMode);
    }

    public function getTemplate()
    {
        return $this->hasOne(Template::class, ['id' => 'template_id']);
    }

    public function getObject(): ?CustomPage
    {
        if (empty($this->page_id)) {
            return null;
        }

        return CustomPage::findOne(['id' => $this->page_id]);
    }

    public function getTemplateId()
    {
        return $this->template_id;
    }

    public static function findByOwner(ActiveRecord $owner)
    {
        return self::findOne(['page_id' => $owner->getPrimaryKey()]);
    }

    public static function deleteByOwner(ActiveRecord $owner)
    {
        $container = self::findOne(['page_id' => $owner->getPrimaryKey()]);
        if ($container) {
            return $container->delete();
        }
        return false;
    }

    public function getCacheKey(): string
    {
        $key = get_class($this) . $this->getPrimaryKey();

        $template = $this->template;
        if ($template instanceof Template &&
            $template->getElements()->andWhere(['content_type' => UserContent::class])->exists()) {
            // If this template contains at least one element with user data,
            // then cache it per current user in order to display user elements correctly
            $key .= '-' . Yii::$app->user->id;
        }

        return $key;
    }
}
