<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\elements;

use humhub\modules\content\widgets\ContainerTagPicker;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\SpacePickerField;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Url;

/**
 * Class to manage content records of the elements with Spaces list
 *
 * Dynamic attributes:
 * @property array $member
 * @property string $memberType
 * @property array $tag
 * @property int $limit
 */
class SpacesElement extends BaseContentContainersElement
{
    public const RECORD_CLASS = Space::class;

    /**
     * @inheritdoc
     */
    public function getLabel(): string
    {
        return Yii::t('CustomPagesModule.template', 'Spaces');
    }

    /**
     * @inheritdoc
     */
    protected function getDynamicAttributes(): array
    {
        return array_merge(parent::getDynamicAttributes(), [
            'member' => null,
            'memberType' => null,
            'tag' => null,
            'limit' => null,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'static' => Yii::t('CustomPagesModule.template', 'Select spaces'),
            'member' => Yii::t('CustomPagesModule.template', 'User'),
            'memberType' => Yii::t('CustomPagesModule.template', 'Space member type'),
            'tag' => Yii::t('CustomPagesModule.template', 'Tag'),
            'limit' => Yii::t('CustomPagesModule.template', 'Limit'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return array_merge(parent::attributeHints(), [
            'member' => Yii::t('CustomPagesModule.template', 'When no user is selected, the current logged in user will be used.'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getTypes(): array
    {
        return array_merge(parent::getTypes(), [
            'member' => Yii::t('CustomPagesModule.template', 'Spaces where the user is'),
            'tag' => Yii::t('CustomPagesModule.template', 'Spaces with a specific tag'),
        ]);
    }

    public function getMemberTypes(): array
    {
        return [
            'member' => Yii::t('CustomPagesModule.template', 'Member'),
            'not-member' => Yii::t('CustomPagesModule.template', 'Not member'),
        ];
    }

    public function getTags(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function getQuery(): ActiveQuery
    {
        $query = Space::find()->visible();

        return match ($this->type) {
            'member' => $this->filterMember($query),
            'tag' => $this->filterTag($query),
            default => $this->filterStatic($query),
        };
    }

    protected function filterMember(ActiveQuery $query): ActiveQuery
    {
        $userGuid = $this->member ?: Yii::$app->user->getGuid();

        if (empty($this->memberType) || empty($userGuid)) {
            return $query->andWhere(false);
        }

        return match ($this->memberType) {
            'member' => $query->leftJoin('space_membership', 'space_membership.space_id = space.id')
                ->leftJoin('user', 'user.id = space_membership.user_id')
                ->andWhere(['user.guid' => $userGuid])
                ->andWhere(['space_membership.status' => Membership::STATUS_MEMBER]),
            'not-member' => $query->andWhere(['NOT IN', 'space.id', Membership::find()
                ->select('space_membership.space_id')
                ->leftJoin('user', 'space_membership.user_id = user.id')
                ->where(['space_membership.status' => Membership::STATUS_MEMBER])
                ->andWhere(['user.guid' => $userGuid])]),
            default => $query->andWhere(false),
        };
    }

    protected function filterTag(ActiveQuery $query): ActiveQuery
    {
        return $query->leftJoin('contentcontainer_tag_relation', 'contentcontainer_tag_relation.contentcontainer_id = space.contentcontainer_id')
            ->leftJoin('contentcontainer_tag', 'contentcontainer_tag.id = contentcontainer_tag_relation.tag_id')
            ->andWhere(['contentcontainer_tag.contentcontainer_class' => Space::class])
            ->andWhere(['contentcontainer_tag.name' => $this->tag]);
    }

    /**
     * @inheritdoc
     */
    protected function isConfigured(): bool
    {
        return parent::isConfigured() || $this->type === 'member';
    }

    /**
     * @inheritdoc
     */
    public function getTemplateVariable(): BaseElementVariable
    {
        return new SpacesElementVariable($this);
    }

    /**
     * @inheritdoc
     */
    public function renderEditForm(ActiveForm $form): string
    {
        return parent::renderEditForm($form)
            . $this->renderEditRecordsTypeFields([
                'static' => $form->field($this, 'static')->widget(SpacePickerField::class, ['minInput' => 2]),
                'member' => $form->field($this, 'member')->widget(UserPickerField::class, ['minInput' => 2, 'maxSelection' => 1])
                            . $form->field($this, 'memberType')->dropDownList($this->getMemberTypes()),
                'tag' => $form->field($this, 'tag')->widget(ContainerTagPicker::class, [
                    'url' => Url::to(['/space/browse/search-tags-json']),
                    'minInput' => 2,
                    'maxSelection' => 1,
                ]),
                'member,tag' => $form->field($this, 'limit'),
            ]);
    }
}
