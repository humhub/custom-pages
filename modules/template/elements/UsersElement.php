<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\user\models\Group;
use humhub\modules\user\models\User;
use humhub\modules\user\widgets\UserPickerField;
use humhub\widgets\form\ActiveForm;
use Yii;
use yii\db\ActiveQuery;

/**
 * Class to manage content records of the elements with Users list
 *
 * Dynamic attributes:
 * @property string $group
 * @property array $friend
 * @property int $limit
 */
class UsersElement extends BaseContentContainersElement
{
    public const RECORD_CLASS = User::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'Users');
    }

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return array_merge(parent::getDynamicAttributes(), [
            'group' => null,
            'friend' => null,
            'limit' => null,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'static' => Yii::t('CustomPagesModule.template', 'Select users'),
            'group' => Yii::t('CustomPagesModule.template', 'Select group'),
            'friend' => Yii::t('CustomPagesModule.template', 'User'),
            'limit' => Yii::t('CustomPagesModule.template', 'Limit'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            'friend' => Yii::t('CustomPagesModule.template', 'When no user is selected, the current logged in user will be used.'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            'group' => Yii::t('CustomPagesModule.template', 'Users from the selected group'),
            'friend' => Yii::t('CustomPagesModule.template', 'Users where the user is friend of'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['group'], 'in', 'range' => array_keys($this->getGroupOptions())],
            [['friend'], 'safe'],
            [['limit'], 'integer'],
        ]);
    }

    public function getGroupOptions(): array
    {
        $groups = Yii::$app->user->isAdmin()
            ? Group::find()->all()
            : Group::findAll(['is_admin_group' => '0']);

        $options = [];
        foreach ($groups as $group) {
            $options[$group->id] = $group->name . ($group->is_default_group ? ' (' . Yii::t('AdminModule.base', 'Default') . ')' : '');
        }

        return $options;
    }

    /**
     * @inheritdoc
     */
    protected function getQuery(): ActiveQuery
    {
        $query = User::find()->visible();

        return match ($this->type) {
            'group' => $this->filterGroup($query),
            'friend' => $this->filterFriend($query),
            default => $this->filterStatic($query),
        };
    }

    protected function filterGroup(ActiveQuery $query): ActiveQuery
    {
        return $query->leftJoin('group_user', 'group_user.user_id = user.id')
            ->andWhere(['group_user.group_id' => $this->group]);
    }

    protected function filterFriend(ActiveQuery $query): ActiveQuery
    {
        $friendGuid = $this->friend ?: Yii::$app->user->getGuid();

        if (empty($friendGuid)) {
            return $query->andWhere(false);
        }

        return $query->leftJoin('user_friendship', 'user_friendship.user_id = user.id')
            ->leftJoin('user AS friend', 'user_friendship.friend_user_id = friend.id')
            ->andWhere(['friend.guid' => $friendGuid]);
    }

    /**
     * @inheritdoc
     */
    protected function isConfigured(): bool
    {
        return parent::isConfigured() || $this->type === 'friend';
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return new UsersElementVariable($this);
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        return parent::renderEditForm($form)
            . $this->renderEditRecordsTypeFields([
                'static' => $form->field($this, 'static')->widget(UserPickerField::class, ['minInput' => 2]),
                'group' => $form->field($this, 'group')->dropDownList($this->getGroupOptions()),
                'friend' => $form->field($this, 'friend')->widget(UserPickerField::class, ['minInput' => 2, 'maxSelection' => 1]),
                'group,friend' => $form->field($this, 'limit'),
            ]);
    }
}
