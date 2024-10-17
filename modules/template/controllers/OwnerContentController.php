<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use humhub\modules\content\interfaces\ContentOwner;
use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use Yii;
use humhub\modules\custom_pages\modules\template\widgets\EditElementModal;
use humhub\modules\custom_pages\modules\template\models\OwnerContentVariable;
use humhub\modules\custom_pages\modules\template\models\forms\EditOwnerContentForm;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\forms\EditMultipleElementsForm;
use humhub\modules\custom_pages\modules\template\widgets\EditMultipleElementsModal;
use yii\base\Response;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * This controller is used to manage OwnerContent instances for TemplateContentOwner.
 *
 * @author buddha
 */
class OwnerContentController extends \humhub\components\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            ['class' => 'humhub\modules\custom_pages\modules\template\components\TemplateAccessFilter'],
        ];
    }

    /**
     * Owner Model Class of the TemplateContentOwner.
     * @var string
     */
    public $ownerModel;

    /**
     * Owner Model Id of the TemplateContentOwner.
     * @var int
     */
    public $ownerId;

    /**
     * The placeholder name of the TemplateElement.
     * @var string
     */
    public $elementName;

    /**
     * Edits the content of a specific OwnerContent for the given TemplateContentOwner.
     *
     * @return Response
     * @throws HttpException
     */
    public function actionEdit($ownerModel, $ownerId, $name)
    {
        $form = new EditOwnerContentForm();
        $form->setElementData($ownerModel, $ownerId, $name);
        $form->setScenario('edit');

        if ($form->load(Yii::$app->request->post())) {
            if ($form->save()) {
                TemplateCache::flushByOwnerContent($form->ownerContent);
                $wrapper = new OwnerContentVariable(['ownerContent' => $form->ownerContent]);
                return $this->getJsonEditElementResult(true, $wrapper->render(true));
            } else {
                return $this->getJsonEditElementResult(false, $this->renderAjaxPartial(EditElementModal::widget([
                    'model' => $form,
                    'title' => Yii::t('CustomPagesModule.base', '<strong>Edit</strong> {type} element', ['type' => $form->getLabel()]),
                ])));
            }
        }

        return $this->asJson([
            'output' => $this->renderAjaxPartial(EditElementModal::widget([
                'model' => $form,
                'title' => Yii::t('CustomPagesModule.base', '<strong>Edit</strong> {type} element', ['type' => $form->getLabel()]),
            ])),
        ]);
    }

    /**
     * Used to delete owner content models.
     *
     * @return Response
     * @throws HttpException
     */
    public function actionDelete()
    {
        $this->forcePostRequest();

        $ownerModel = Yii::$app->request->post('ownerModel');
        $ownerId = Yii::$app->request->post('ownerId');
        $name = Yii::$app->request->post('name');

        if (!$ownerModel || !$ownerId || !$name) {
            throw new HttpException(400, Yii::t('CustomPagesModule.base', 'Invalid request data!'));
        }

        $form = new EditOwnerContentForm();
        $form->setElementData($ownerModel, $ownerId, $name);

        $this->deleteOwnerContent($form->ownerContent);

        // Set our original owner for this element block
        $variable = new OwnerContentVariable(['ownerContent' => $form->element->getDefaultContent(true), 'options' => [
            'owner_model' => $ownerModel,
            'owner_id' => $ownerId,
        ]]);

        return $this->getJsonEditElementResult(true, $variable->render(true));
    }

    /**
     * Used to delete owner content models by Content.
     *
     * @return Response
     * @throws HttpException
     */
    public function actionDeleteByContent()
    {
        $this->forcePostRequest();

        $contentModel = Yii::$app->request->post('contentModel');
        $contentId = Yii::$app->request->post('contentId');

        if (!$contentModel || !$contentId) {
            throw new HttpException(400, Yii::t('CustomPagesModule.base', 'Invalid request data!'));
        }

        $ownerContent = OwnerContent::findByContent($contentModel, $contentId);

        $this->deleteOwnerContent($ownerContent);

        return $this->asJson(['success' => true]);
    }

    private function deleteOwnerContent($ownerContent)
    {
        if (!$ownerContent instanceof OwnerContent) {
            throw new NotFoundHttpException();
        }
        // Do not allow the deletion of default content this is only allowed in admin controller.
        if ($ownerContent->isDefault()) {
            throw new HttpException(403, Yii::t('CustomPagesModule.base', 'You are not allowed to delete default content!'));
        }
        if ($ownerContent->isEmpty()) {
            throw new HttpException(400, Yii::t('CustomPagesModule.base', 'Empty content elements cannot be deleted!'));
        }

        TemplateCache::flushByOwnerContent($ownerContent);

        return $ownerContent->delete();
    }

    /**
     * Action for editing all owner content models for a given template instance in one view.
     *
     * @param int $id
     * @return Response
     */
    public function actionEditMultiple($id)
    {
        $templateInstance = TemplateInstance::findOne(['id' => $id]);

        $form = new EditMultipleElementsForm();
        $form->editDefault = false;
        $form->setOwner($templateInstance, $templateInstance->template_id);

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByTemplateInstance($templateInstance);
            return $this->asJson(['success' => true]);
        }

        return $this->asJson([
            'output' => $this->renderAjaxPartial(EditMultipleElementsModal::widget([
                'model' => $form,
                'title' => Yii::t('CustomPagesModule.template', '<strong>Edit</strong> elements of {templateName}', ['templateName' => $form->template->name]),
            ])),
        ]);
    }

    /**
     * Creates a json result array used by multiple actions.
     *
     * @param bool $success defines if the process was successfull e.g. saving an element
     * @param mixed $content content result
     * @param mixed $form Form model
     * @return Response
     */
    private function getJsonEditElementResult($success, $content)
    {
        $json = [];
        $json['success'] = $success;
        $json['output'] = $content;
        $json['ownerModel'] = Yii::$app->request->get('ownerModel');
        $json['ownerId'] = Yii::$app->request->get('ownerId');
        $json['name'] = Yii::$app->request->get('name');
        return $this->asJson($json);
    }

}
