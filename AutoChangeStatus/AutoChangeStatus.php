<?php
# MantisBT - A PHP based bugtracking system
# MantisBT is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 2 of the License, or
# (at your option) any later version.
#
# MantisBT is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with MantisBT.  If not, see <http://www.gnu.org/licenses/>.

#
#  Autochange status Plugin for Mantis BugTracker :
#  © Hennes Hervé <contact@h-hennes.fr>
#    2015-2016
#  http://www.h-hennes.fr/blog/

class AutoChangeStatusPlugin extends MantisPlugin
{

    function register() {
        $this->name =  plugin_lang_get('plugin_title');
        $this->description = plugin_lang_get('plugin_description');
        $this->page = 'config.php';
        $this->version = '0.1.3';
        $this->requires = array(
            'MantisCore' => '1.3.0',
        );
        $this->author = 'Hennes Hervé';
        $this->url = 'http://www.h-hennes.fr';
    }

    /**
     * Configuration par défaut du module
     */
    function config() {

        return array(
            'change_status_user' => 'administrator',
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
