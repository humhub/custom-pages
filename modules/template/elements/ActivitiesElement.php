<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\activity\models\Activity;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\SpacePickerField;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\form\ActiveForm;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class to manage Activity records of the elements with Activities list
 *
 * Dynamic attributes:
 * @property array $spaceSelection
 * @property array $space
 * @property array $author
 * @property int $limit
 */
class ActivitiesElement extends BaseRecordsElement
{
    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.base', 'Activities');
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return new ActivitiesElementVariable($this);
    }

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return [
            'spaceSelection' => 'include',
            'space' => null,
            'author' => null,
            'limit' => null,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'spaceSelection' => Yii::t('CustomPagesModule.base', 'Space selection'),
            'space' => Yii::t('CustomPagesModule.base', 'Spaces'),
            'author' => Yii::t('CustomPagesModule.base', 'Authors'),
            'limit' => Yii::t('CustomPagesModule.base', 'Limit'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            'spaceSelection' => Yii::t('CustomPagesModule.base', 'Determine which Spaces should be taken into account.'),
            'space' => Yii::t('CustomPagesModule.base', 'Leave empty to list all records related to the current user.'),
        ]);
    }

    /**
     * @inheritdoc
     */
    protected function getQuery(): ActiveQuery
    {
        $query = Activity::find()->visible();

        if (!empty($this->space)) {
            $query->joinWith(['contentContainer'])
                ->leftJoin(
                    Space::tableName(),
                    ContentContainer::tableName() . '.pk = ' . Space::tableName() . '.id AND '
                        . ContentContainer::tableName() . '.class = :spaceClass',
                    [':spaceClass' => Space::class],
                )
                ->andWhere([$this->spaceSelection === 'exclude' ? 'NOT IN' : 'IN', Space::tableName() . '.guid', $this->space]);
        }

        if (!empty($this->author)) {
            $query->andWhere([User::tableName() . '.guid' => $this->author]);
        }

        if (!Yii::$app->user->isGuest) {
            $query->excludeUser(Yii::$app->user->getIdentity());
        }

        $query->orderBy([Activity::tableName() . '.created_at' => SORT_DESC]);

        return $query->limit($this->limit);
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        return $form->field($this, 'spaceSelection')->dropDownList([
            'include' => Yii::t('CustomPagesModule.base', 'Include selected Spaces only'),
            'exclude' => Yii::t('CustomPagesModule.base', 'Include all Spaces except selected'),
        ])
            . $form->field($this, 'space')->widget(SpacePickerField::class)
            . $form->field($this, 'author')->widget(UserPickerField::class)
            . $form->field($this, 'limit');
    }
}
