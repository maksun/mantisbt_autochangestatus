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

require_once( dirname(__FILE__) . '/../../../core.php' );

#En cron on push le nom du module
plugin_push_current('AutoChangeStatus');

#Utilisateur par défaut
$g_cron_current_user_id = plugin_config_get('change_status_user');

#Nom des statuts avec les traductions
$t_status_names = MantisEnum::getAssocArrayIndexedByValues( lang_get( 'status_enum_string' ) );

#Requête pour récupérer la date de modification du statut avec le nombre de jours correspondants
    $sql_status = "SELECT h.* , FROM_UNIXTIME(date_modified, '%Y-%m-%d') AS date_status_modified
             FROM {bug} b
             LEFT JOIN {bug_history} h ON ( b.id = h.bug_id AND field_name='status' AND new_value=".db_param()." )
             WHERE b.status = ".db_param()." AND b.project_id = ".db_param()."
             AND CURDATE() = DATE_ADD(FROM_UNIXTIME(date_modified, '%Y-%m-%d'), INTERVAL ".db_param()." DAY)";

	#@ToDO : Essayer de grouper les requêtes
	$sql_status_no_project =  "SELECT h.* , FROM_UNIXTIME(date_modified, '%Y-%m-%d') AS date_status_modified
             FROM {bug} b
             LEFT JOIN {bug_history} h ON ( b.id = h.bug_id AND field_name='status' AND new_value=".db_param()." )
             WHERE b.status = ".db_param()."
             AND CURDATE() = DATE_ADD(FROM_UNIXTIME(date_modified, '%Y-%m-%d'), INTERVAL ".db_param()." DAY)";

#Requête pour récupérer la date de la dernière note UTILISATEUR sur le bug
    $sql_notes = "SELECT * FROM {bugnote}
                  WHERE bug_id= ".db_param().
        " AND reporter_id <> ".plugin_config_get('change_status_user').
        " AND date_submitted > ".db_param();


#Récupération des changements automatiques qui sont actifs
$change_status = db_query("SELECT * FROM {plugin_autochangestatus} WHERE active=1");

#Boucle de traitement
while ($change = db_fetch_array($change_status)) {

####
# 1ère étape : Ajout des notes de rappel ( si actif )
###
    if ($change['reminder'] == 1) {

        #Récupération des bugs éligibles à l'ajout d'une note de rappel
		if ( $change['project_id'] != 0 ) {
			$t_bug_notes_pool = db_query_bound($sql_status,
			    array($change['from_status'], $change['from_status'], $change['project_id'],
			    $change['reminder_days']));
		} else {
			$t_bug_notes_pool = db_query_bound($sql_status_no_project,
                array($change['from_status'], $change['from_status'],$change['reminder_days']));
		}

        while ($t_bug = db_fetch_array($t_bug_notes_pool)) {

            #On regarde si ces bugs ont été commenté dans la période
            $t_user_notes = db_query_bound($sql_notes,
                array($t_bug['bug_id'], $t_bug['date_modified']));

            #Si pas de notes on en rajoute une
            if (db_num_rows($t_user_notes) < 1) {

                $t_bugnote_text = sprintf(plugin_lang_get('before_change_status_message'),$t_status_names[$change['from_status']],$change['reminder_days'],$t_status_names[$change['to_status']],($change['status_days'] - $change['reminder_days']));
                bugnote_add( $t_bug['bug_id'], $t_bugnote_text,'0:02', false, BUGNOTE,'', $g_cron_current_user_id);
            
            }
        }
    }

####
# 2ème étape : Changement automatique des statuts
###
    
	if ( $change['project_id'] != 0 ) {
        $t_bug_status_pool = db_query_bound($sql_status,
            array($change['from_status'], $change['from_status'], $change['project_id'],
            $change['status_days']));
	} else {
	    $t_bug_status_pool = db_query_bound($sql_status_no_project,
            array($change['from_status'], $change['from_status'],$change['status_days']));
	}

     while ($t_bug_status = db_fetch_array($t_bug_status_pool)) {

            #On regarde si ces bugs ont été commenté dans la période
            $t_user_notes_status = db_query_bound($sql_notes,
                array($t_bug_status['bug_id'], $t_bug_status['date_modif']));

            #Si pas de notes on en rajoute une
            if (db_num_rows($t_user_notes_status) < 1) {

                #Rajout d'une note informative
                $t_bugnote_text_status = sprintf(plugin_lang_get('change_status_message'),$change['status_days']);
                bugnote_add( $t_bug_status['bug_id'], $t_bugnote_text_status,'0:02', false, BUGNOTE,'', $g_cron_current_user_id);

                #Changement du status du bug
                $t_bug_model = bug_get($t_bug_status['bug_id']);
                $t_bug_model->status = $change['to_status'];
                $t_bug_model->update();
        }
     }
}  