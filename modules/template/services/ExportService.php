<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\custom_pages\modules\template\services;

use humhub\modules\custom_pages\modules\template\models\OwnerContent;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TemplateContentActiveRecord;
use Yii;
use yii\web\Response;

class ExportService
{
    private Template $template;
    private ?array $data = null;

    public function __construct(Template $template)
    {
        $this->template = $template;
    }

    public static function instance(Template $template): self
    {
        return new self($template);
    }

    public function export(): self
    {
        $this->data = $this->template->attributes;

        $this->data['elements'] = [];
        foreach ($this->template->elements as $e => $element) {
            $this->data['elements'][$e] = $element->attributes;

            $defaultContent = $element->getDefaultContent();
            if ($defaultContent instanceof OwnerContent) {
                $this->data['elements'][$e]['ownerContent'] = $defaultContent->attributes;

                $templateContent = $defaultContent->getInstance();
                if ($templateContent instanceof TemplateContentActiveRecord) {
                    $this->data['elements'][$e]['templateContent'] = $templateContent->attributes;
                }
            }
        }

        return $this;
    }

    private function getFileName(): string
    {
        return $this->template->name . '_' . date('Y-m-d_H-i') . '.json';
    }

    public function send(): Response
    {
        return Yii::$app->response->sendContentAsFile(json_encode($this->data), $this->getFileName());
    }
}
