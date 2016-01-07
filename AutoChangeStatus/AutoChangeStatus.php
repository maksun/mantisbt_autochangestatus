<?php

/*
  Plugin AutoChangeStatus pour Mantis BugTracker :
  - Changement automatique de statuts des bugs
  © Hennes Hervé - 2015
  http://www.h-hennes.fr
 */
class AutoChangeStatusPlugin extends MantisPlugin
{

    function register() {
        $this->name =  plugin_lang_get('plugin_title');
        $this->description = plugin_lang_get('plugin_description');
        $this->page = 'config.php';
        $this->version = '0.1.3';
        $this->requires = array(
            'MantisCore' => '1.2.0',
        );
        $this->author = 'Hennes Hervé';
        $this->url = 'http://www.h-hennes.fr';
    }

    /**
     * Configuration par défaut du module
     */
    function config() {

        return array(
            'change_status_user' => 'eibot',
        );
    }

    /**
     * Installation du plugin
     */
    public function install(){

        return db_query("CREATE TABLE IF NOT EXISTS `mantis_autochange_status` (
                        `changestatus_id` int(11) NOT NULL AUTO_INCREMENT,
                        `project_id` int(11) NOT NULL,
                        `from_status` int(3) NOT NULL,
                        `to_status` int(3) NOT NULL,
                        `status_days` int(3) NOT NULL,
                        `reminder` tinyint(1) NOT NULL,
                        `reminder_message` varchar(255) NOT NULL,
                        `reminder_days` int(11) NOT NULL,
                        `active` tinyint(1) NOT NULL,
                        PRIMARY KEY (`changestatus_id`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");

    }

    /**
     * Desinstallation du plugin
     */
    public function uninstall(){
        return db_query('DROP TABLE '.db_get_table('autochange_status'));
    }

}
?>
