<?php
namespace humhub\modules\custom_pages\models;
use Yii;
use yii\base\Model;

/**
 * CurrentUserGroup selects the current users group
 *
 * @author hannesmichael
 */

class CurrentUserGroup extends Model {

    public static function find()   {
        $selectGroups = 'SELECT group_id FROM `user` WHERE id='.Yii::$app->user->id;

        return Yii::$app->db->createCommand($selectGroups)->queryOne();
    }

}
