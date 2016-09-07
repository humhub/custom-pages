<?php

use humhub\components\Migration;
use humhub\modules\custom_pages\modules\template\models\Template;
use humhub\modules\custom_pages\modules\template\models\TextContent;
use humhub\modules\custom_pages\modules\template\models\FileContent;
use humhub\modules\custom_pages\modules\template\models\ContainerContent;
use humhub\modules\custom_pages\modules\template\models\RichtextContent;

class m160907_175706_default_templates extends Migration
{

    public function up()
    {
        /**
         * 
         * Two column template
         * 
         */
        $twoColumnTemplateId = $this->insertTwoColumnTemplate();
        // Insert elements
        $this->insertTemplateElement($twoColumnTemplateId, 'content', ContainerContent::className());
        $this->insertTemplateElement($twoColumnTemplateId, 'sidebar_container', ContainerContent::className());
        
        // Insert default container definition for content container
        $this->insertSilent('custom_pages_template_container_content_definition', ['allow_multiple' => 1, 'is_inline' => 0, 'is_default' => 1]);
        $this->insertSilent('custom_pages_template_container_content', ['definition_id' => $this->db->getLastInsertID()]);
        $this->insertSilent('custom_pages_template_owner_content', [
                'element_name' => 'content',
                'owner_model' => Template::className(),
                'owner_id' => $twoColumnTemplateId,
                'content_type' => ContainerContent::className(),
                'content_id' => $this->db->getLastInsertID()
            ]);
        
        // Insert default content for sidebar container
        $this->insertSilent('custom_pages_template_container_content_definition', ['allow_multiple' => 1, 'is_inline' => 0, 'is_default' => 1]);
        $this->insertSilent('custom_pages_template_container_content', ['definition_id' => $this->db->getLastInsertID()]);
        $this->insertSilent('custom_pages_template_owner_content', [
                'element_name' => 'sidebar_container',
                'owner_model' => Template::className(),
                'owner_id' => $twoColumnTemplateId,
                'content_type' => ContainerContent::className(),
                'content_id' => $this->db->getLastInsertID()
            ]);
        
        /**
         * 
         * One Column Template
         * 
         */
        $oneColumnTemplateId = $this->insertOneColumnTemplate();
        
        // Insert elements
        $this->insertTemplateElement($oneColumnTemplateId, 'content', ContainerContent::className());
        
        // Insert default content definition
        $this->insertSilent('custom_pages_template_container_content_definition', ['allow_multiple' => 1, 'is_inline' => 0, 'is_default' => 1]);
        $this->insertSilent('custom_pages_template_container_content', ['definition_id' => $this->db->getLastInsertID()]);  
        $this->insertSilent('custom_pages_template_owner_content', [
                'element_name' => 'content',
                'owner_model' => Template::className(),
                'owner_id' => $oneColumnTemplateId,
                'content_type' => ContainerContent::className(),
                'content_id' => $this->db->getLastInsertID()
            ]);

        /**
         * 
         * Headline Container
         * 
         */
        $headlineTmplId = $this->insertHeadLineTemplate();
        // Insert elements
        $this->insertTextTemplateElement($headlineTmplId, 'heading', 'My Headline');
        $this->insertTextTemplateElement($headlineTmplId, 'subheading', 'My Subheadline');
        $this->insertTemplateElement($headlineTmplId, 'background', FileContent::className());
        
        /**
         * 
         * Article Container
         * 
         */
        $articlelineTmplId = $this->insertArticleTemplate();
        // Insert elements
        $this->insertRichtextTemplateElement($articlelineTmplId, 'content', $this->getDefaultArticleRichtext());
        
        /**
         * 
         * Snippet Layout Template
         * 
         */
        $snippetLayoutTemplateId = $this->insertSnippetLayoutTemplate();
        
        // Insert elements
        $this->insertTemplateElement($snippetLayoutTemplateId, 'heading', ContainerContent::className());
        
        // Insert default content definition
        $this->insertSilent('custom_pages_template_container_content_definition', ['allow_multiple' => 0, 'is_inline' => 0, 'is_default' => 1]);
        $definitionId = $this->db->getLastInsertID();
        $this->insertSilent('custom_pages_template_container_content_template', ['template_id' => $headlineTmplId, 'definition_id' => $definitionId]);
        $this->insertSilent('custom_pages_template_container_content', ['definition_id' => $definitionId]);  
        $this->insertSilent('custom_pages_template_owner_content', [
                'element_name' => 'heading',
                'owner_model' => Template::className(),
                'owner_id' => $snippetLayoutTemplateId,
                'content_type' => ContainerContent::className(),
                'content_id' => $this->db->getLastInsertID()
            ]);
        
        $this->insertRichtextTemplateElement($snippetLayoutTemplateId, 'content', '<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>');
    }
    
    public function insertSnippetLayoutTemplate()
    {
        $this->insertSilent('custom_pages_template', [
            'name' => 'system_simple_snippet_layout',
            'engine' => 'twig',
            'description' => 'Simple snippet layout with head container and richtext.',
            'source' => $this->getSnippetLayoutSource(), 
            'type' => Template::TYPE_SNIPPED_LAYOUT,
            'created_at' => new \yii\db\Expression('NOW()')]);

        return $this->db->getLastInsertID();
    }

    public function insertTwoColumnTemplate()
    {
        $this->insertSilent('custom_pages_template', [
            'name' => 'system_two_column_layout',
            'engine' => 'twig',
            'description' => 'Simple two column layout.',
            'source' => $this->getTwoColumnSource(), 
            'type' => Template::TYPE_LAYOUT,
            'created_at' => new \yii\db\Expression('NOW()')]);

        return $this->db->getLastInsertID();
    }
    
    public function insertOneColumnTemplate()
    {
        $this->insertSilent('custom_pages_template', [
            'name' => 'system_one_column_layout',
            'engine' => 'twig',
            'description' => 'Simple one column layout.',
            'source' => $this->getOneColumnSource(), 
            'type' => Template::TYPE_LAYOUT,
            'created_at' => new \yii\db\Expression('NOW()')]);

        return $this->db->getLastInsertID();
    }

    public function insertTemplateElement($tmplid, $name, $contentType)
    {
        $this->insertSilent('custom_pages_template_element', [
            'template_id' => $tmplid,
            'name' => $name,
            'content_type' => $contentType
        ]);
    }

    public function insertTextTemplateElement($tmplid, $name, $default = null)
    {
        $this->insertTemplateElement($tmplid, $name, TextContent::className());

        if ($default != null) {
            $this->insertSilent('custom_pages_template_text_content', [
                'content' => $default
            ]);

            $this->insertSilent('custom_pages_template_owner_content', [
                'element_name' => $name,
                'owner_model' => Template::className(),
                'owner_id' => $tmplid,
                'content_type' => TextContent::className(),
                'content_id' => $this->db->getLastInsertID()
            ]);
        }
    }
    
    public function insertRichTextTemplateElement($tmplid, $name, $default = null)
    {
        $this->insertTemplateElement($tmplid, $name, RichtextContent::className());

        if ($default != null) {
            $this->insertSilent('custom_pages_template_richtext_content', [
                'content' => $default
            ]);

            $this->insertSilent('custom_pages_template_owner_content', [
                'element_name' => $name,
                'owner_model' => Template::className(),
                'owner_id' => $tmplid,
                'content_type' => RichtextContent::className(),
                'content_id' => $this->db->getLastInsertID()
            ]);
        }
    }

    public function insertHeadLineTemplate()
    {
        $this->insertSilent('custom_pages_template', [
            'name' => 'system_headline_container',
            'engine' => 'twig',
            'description' => 'Simple headline row with background image.',
            'source' => $this->getHeadLineSource(), 
            'type' => Template::TYPE_CONTAINER,
            'created_at' => new \yii\db\Expression('NOW()')]);

        return $this->db->getLastInsertID();
    }
    
    public function insertArticleTemplate()
    {
        $this->insertSilent('custom_pages_template', [
            'name' => 'system_article_container',
            'engine' => 'twig',
            'description' => 'Simple richtext article.',
            'source' => $this->getArticleSource(), 
            'type' => Template::TYPE_CONTAINER,
            'created_at' => new \yii\db\Expression('NOW()')]);

        return $this->db->getLastInsertID();
    }
    
    public function getOneColumnSource()
    {
        return <<< EOT
<div class="row">
	<div class="col-md-12">
            <div class="panel panel-default">
			<div class="panel-body">
                            {{ content }}
                        </div>
            </div>
	</div>
</div>
EOT;
    }
    
    public function getDefaultArticleRichtext()
    {
        return <<< EOT
<h1>This is a&nbsp;simple article!</h1>

<hr />
<p>Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.</p>

EOT;
    }
    
    public function getTwoColumnSource()
    {
        return <<< EOT
<div class="row">
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-body">
				{{ content }}
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-body">
				{{ sidebar_container }}
			</div>
		</div>	
	</div>
</div>
EOT;
    }
    
    public function getSnippetLayoutSource()
    {
        return <<< EOT
<div>
        {{ heading }}
</div>
<div style="margin-top:15px;">
	{{ content }}
</div>
EOT;
    }
    
    
     public function getArticleSource()
    {
        return <<< EOT
<div style="margin-top:15px;">
	<div style="padding:0 15px;">
		{{ content }}
	</div>
</div>
EOT;
    }
    

    public function getHeadLineSource()
    {
        return <<< EOT
{% if background.empty %}
    {% set bg = assets['bgImage2.jpg']  %}
{% else %}
    {% set bg =  background %}
{% endif %}

<div style="height:218px;overflow:hidden;color:#fff;background-image: url('{{ bg }}');background-position:50% 50%;text-align:center;">
	<div style="padding-top:40px;">
		<h1 style="color:#fff;font-size:36px;margin:20px 0 10px;">{{ heading }}</h1>
		<hr style="max-width:100px;border-width:3px;">
		 <span>{{ subheading }}</span>
  	 </div>
</div>
EOT;
    }

    public function down()
    {
        echo "m160907_175706_default_templates cannot be reverted.\n";

        return true;
    }

    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
