<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\controllers;

use humhub\components\Controller;
use humhub\modules\custom_pages\modules\template\elements\BaseTemplateElementContent;
use humhub\modules\custom_pages\modules\template\models\forms\EditElementContentForm;
use humhub\modules\custom_pages\modules\template\widgets\EditElementModal;
use humhub\modules\custom_pages\modules\template\models\ElementContentVariable;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\forms\EditMultipleElementsForm;
use humhub\modules\custom_pages\modules\template\widgets\EditMultipleElementsModal;
use Yii;
use yii\base\Response;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * This controller is used to manage TemplateElementContent instances.
 *
 * @author buddha
 */
class ElementContentController extends Controller
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
     * Edits the content of a specific Element Content for the given TemplateContentOwner.
     *
     * @return Response
     */
    public function actionEdit($elementContentId, $templateInstanceId)
    {
        $form = new EditElementContentForm();
        $form->setElementData($elementContentId, $templateInstanceId);
        $form->setScenario('edit');

        if ($form->load(Yii::$app->request->post())) {
            if ($form->save()) {
                TemplateCache::flushByElementContent($form->content);
                $wrapper = new ElementContentVariable(['elementContent' => $form->content]);
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
     * Used to delete element content record.
     *
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionDelete()
    {
        $this->forcePostRequest();

        $elementContentId = Yii::$app->request->post('elementContentId');
        $templateInstanceId = Yii::$app->request->post('templateInstanceId');

        if (!$elementContentId || !$templateInstanceId) {
            throw new BadRequestHttpException('Invalid request data!');
        }

        $form = new EditElementContentForm();
        $form->setElementData($elementContentId, $templateInstanceId);

        $this->deleteElementContent($form->elementContent);

        // Set the default content for this element block
        $variable = new ElementContentVariable([
            'elementContent' => $form->element->getDefaultContent(true),
            'options' => [
                'template_instance_id' => $templateInstanceId,
            ],
        ]);

        return $this->getJsonEditElementResult(true, $variable->render(true));
    }

    /**
     * Used to delete element content models by Content.
     *
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionDeleteByContent()
    {
        $this->forcePostRequest();

        $elementContentId = Yii::$app->request->post('elementContentId');

        if (!$elementContentId) {
            throw new BadRequestHttpException('Invalid request data!');
        }

        $elementContent = BaseTemplateElementContent::findOne($elementContentId);

        $this->deleteElementContent($elementContent);

        return $this->asJson(['success' => true]);
    }

    private function deleteElementContent($elementContent)
    {
        if (!$elementContent instanceof BaseTemplateElementContent) {
            throw new NotFoundHttpException();
        }
        // Do not allow the deletion of default content this is only allowed in admin controller.
        if ($elementContent->isDefault()) {
            throw new ForbiddenHttpException(Yii::t('CustomPagesModule.base', 'You are not allowed to delete default content!'));
        }
        if ($elementContent->isEmpty()) {
            throw new BadRequestHttpException(Yii::t('CustomPagesModule.base', 'Empty content elements cannot be deleted!'));
        }

        TemplateCache::flushByElementContent($elementContent);

        return $elementContent->delete();
    }

    /**
     * Action for editing all element content models for a given template instance in one view.
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
     * @param bool $success defines if the process was successful e.g. saving an element
     * @param mixed $content content result
     * @return Response
     */
    private function getJsonEditElementResult(bool $success, string $content): Response
    {
        return $this->asJson([
            'success' => $success,
            'output' => $content,
        ]);
    }

}
