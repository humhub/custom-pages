<?php

namespace humhub\modules\custom_pages\modules\template\controllers;

use Yii;
use humhub\modules\custom_pages\modules\template\widgets\ConfirmDeletionModal;
use humhub\modules\custom_pages\modules\template\widgets\EditElementModal;
use humhub\modules\custom_pages\modules\template\models\OwnerContentVariable;
use humhub\modules\custom_pages\modules\template\models\forms\EditOwnerContentForm;
use humhub\modules\custom_pages\modules\template\components\TemplateCache;
use humhub\modules\custom_pages\modules\template\models\TemplateInstance;
use humhub\modules\custom_pages\modules\template\models\forms\EditMultipleElementsForm;
use humhub\modules\custom_pages\modules\template\widgets\EditMultipleElementsModal;

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
            [
                'class' => 'humhub\modules\custom_pages\modules\template\components\TemplateAccessFilter'
            ],
        ];
    }

    /**
     * Owner Model Class of the TemplateContentOwner.
     * @var string 
     */
    public $ownerModel;

    /**
     * Owner Model Id of the TemplateContentOwner.
     * @var integer 
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
     * @return type
     * @throws \yii\web\HttpException
     */
    public function actionEdit($ownerModel, $ownerId, $name)
    {
        Yii::$app->response->format = 'json';

        $form = new EditOwnerContentForm();
        $form->setElementData($ownerModel, $ownerId, $name);
        $form->setScenario('edit');

        if ($form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByOwnerContent($form->ownerContent);
            $wrapper = new OwnerContentVariable(['ownerContent' => $form->ownerContent]);
            return $this->getJsonEditElementResult(true, $wrapper->render(true));
        }

        return $this->getJsonEditElementResult(false, EditElementModal::widget([
                            'model' => $form,
                            'title' => Yii::t('CustomPagesModule.controllers_TemplateController', '<strong>Edit</strong> {type} element', ['type' => $form->getLabel()])
                        ]), $form);
    }

    /**
     * Used to delete owner content models.
     * 
     * @return type
     * @throws \yii\web\HttpException
     */
    public function actionDelete($ownerModel, $ownerId, $name)
    {
        Yii::$app->response->format = 'json';

        if (Yii::$app->request->post('confirmation')) {
            $form = new EditOwnerContentForm();
            $form->setElementData($ownerModel, $ownerId, $name);

            // Do not allow the deletion of default content this is only allowed in admin controller.
            if ($form->ownerContent->isDefault()) {
                throw new \yii\web\HttpException(403, Yii::t('CustomPagesModule.controllers_TemplateController', 'You are not allowed to delete default content!'));
            } else if ($form->ownerContent->isEmpty()) {
                throw new \yii\web\HttpException(400, Yii::t('CustomPagesModule.controllers_TemplateController', 'Empty content elements cannot be delted!'));
            }

            TemplateCache::flushByOwnerContent($form->ownerContent);
            
            $form->ownerContent->delete();

            // Set our original owner for this element block
            $variable = new OwnerContentVariable(['ownerContent' => $form->element->getDefaultContent(true), 'options' => [
                    'owner_model' => $ownerModel,
                    'owner_id' => $ownerId
            ]]);

            return $this->getJsonEditElementResult(true, $variable->render(true));
        }

        return $this->getJsonEditElementResult(false, ConfirmDeletionModal::widget([
                            'title' => Yii::t('CustomPagesModule.modules_template_controller_OwnerContentController', '<strong>Confirm</strong> content deletion'),
                            'message' => Yii::t('CustomPagesModule.modules_template_widgets_views_confirmDeletionModal', 'Do you really want to delete this content?')
        ]));
    }

    /**
     * Action for editing all owner content models for a given template instance in one view.
     * 
     * @param type $id
     * @return type
     */
    public function actionEditMultiple($id)
    {
        Yii::$app->response->format = 'json';
        
        $templateInstance = TemplateInstance::findOne(['id' => $id]);
        
        $form = new EditMultipleElementsForm();
        $form->editDefault = false;
        $form->setOwner($templateInstance, $templateInstance->template_id);
        
        if (Yii::$app->request->post() && $form->load(Yii::$app->request->post()) && $form->save()) {
            TemplateCache::flushByTemplateInstance($templateInstance);
            return ['success' => true];
        }
        
        return [
            'success' => false,
            'content' => EditMultipleElementsModal::widget([
                'model' => $form,
                'title' => Yii::t('CustomPagesModule.modules_template_controllers_AdminController', '<strong>Edit</strong> elements of {templateName}', ['templateName' => $form->template->name])
            ])
        ];
    }

    /**
     * Creates a json result array used by multiple actions.
     * 
     * @param boolean $success defines if the process was successfull e.g. saving an element
     * @param mixed $content content result
     * @param mixed $form Form model
     * @return type
     */
    private function getJsonEditElementResult($success, $content)
    {
        $json = [];
        $json['success'] = $success;
        $json['content'] = $content;
        $json['ownerModel'] = Yii::$app->request->get('ownerModel');
        $json['ownerId'] = Yii::$app->request->get('ownerId');
        $json['name'] = Yii::$app->request->get('name');
        return $json;
    }
}
