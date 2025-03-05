<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\controllers;

use humhub\components\Controller;
use humhub\modules\custom_pages\modules\template\elements\BaseElementContent;
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

        $elementContent = BaseElementContent::findOne($elementContentId);

        $this->deleteElementContent($elementContent);

        return $this->asJson(['success' => true]);
    }

    private function deleteElementContent($elementContent)
    {
        if (!$elementContent instanceof BaseElementContent) {
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
}
