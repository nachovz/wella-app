<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Poll', 'ivoted');

/**
 * BasePoll
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @property integer $id
 * @property string $question
 * @property timestamp $createdate
 * @property string $status
 * @property string $type
 * @property integer $repeatanswer
 * @property integer $id_user
 * @property string $session_id
 *
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BasePoll extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('poll');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('question', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('createdate', 'timestamp', null, array(
             'type' => 'timestamp',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('status', 'string', 10, array(
             'type' => 'string',
             'length' => 10,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => 'active',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('type', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => 'private',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('repeatanswer', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_user', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('sesion_id', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();

         $this->hasMany('Tag as tg', array('local' => 'id_poll','foreign' => 'id_tag','refClass' => 'TagPoll'));
         $this->hasMany('PollOption as options', array('local' => 'id','foreign' => 'id_poll'));
         $this->hasMany('Answer as answers', array('local' => 'id','foreign' => 'id_poll'));
         $this->hasMany('Way as Ways', array('local' => 'id_poll','foreign' => 'id_way','refClass' => 'PollWay'));
         $this->hasOne('User', array('local' => 'id_user','foreign' => 'id'));

    }
}