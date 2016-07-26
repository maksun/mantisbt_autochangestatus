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

auth_reauthenticate();
access_ensure_global_level(config_get('manage_plugin_threshold'));
html_page_top(plugin_lang_get('title'));
print_manage_menu();


#Gestion de la soumission du formulaire (Création )
if ( gpc_get('submitCreate',false)) {

    $query = "INSERT INTO mantis_autochange_status
        (`project_id`,`from_status`,`to_status`,`status_days`,`reminder`,`reminder_message`,`reminder_days`,`active`)
        VALUES ( ".db_param().",".db_param().",".db_param().",".db_param().",".db_param().",".db_param().",".db_param().",".db_param().")";

    db_query_bound($query,
        array(gpc_get_int('project_id'), gpc_get_int('from_status'), gpc_get_int('to_status'),
        gpc_get_int('status_days'), gpc_get_int('reminder'),
        gpc_get_string('reminder_message'),gpc_get_int('reminder_days'), gpc_get_int('active'))
    );

    print_successful_redirect( plugin_page( 'config', true ) );
}


#Gestion de la soumission du formulaire (Mise à jour )
if ( gpc_get('submitEdit',false)) {

    $query = "UPDATE mantis_autochange_status "
        . "SET `project_id` =".db_param().",`from_status`=".db_param().",`to_status`=".db_param().",`status_days`=".db_param().",`reminder`=".db_param().",`reminder_message`=".db_param().",`reminder_days`=".db_param().",`active`=".db_param().""
        ." WHERE changestatus_id=".db_param();

    db_query_bound($query,
        array(gpc_get_int('project_id'), gpc_get_int('from_status'), gpc_get_int('to_status'),
        gpc_get_int('status_days'), gpc_get_int('reminder'),
        gpc_get_string('reminder_message'),gpc_get_int('reminder_days'), gpc_get_int('active'),gpc_get_int('changestatus_id'))
    );

    print_successful_redirect( plugin_page( 'config', true ) );

}

#Mise à jour récupération des données
if ( $edit_id = gpc_get_int('changestatus_id' , false) ) {
    $change_query = db_query("SELECT * FROM mantis_autochange_status WHERE changestatus_id=".$edit_id);
    $change_datas = db_fetch_array($change_query);
}

#Bug rencontrés avec certains workflow
include_once(dirname(__FILE__).'/functions.php');
$function = 'print_status_option_list_plugin';

$t_projects = project_cache_all();
?>
<h2><?php echo plugin_lang_get('create_new_change_description'); ?></h2>
<form action="<?php echo plugin_page('changestatus') ?>" method="post">
    <table>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('project'); ?></td>
            <td>
                <select name="project_id" >
                    <option value="0"><?php echo plugin_lang_get('all_projects');?></option>
                    <?php foreach ($t_projects as $project): ?>
                    <option value="<?php echo $project['id']; ?>" <?php if ( $project['id'] == $change_datas['project_id'] ):?> selected="selected" <?php endif; ?>>
                        <?php echo $project['name']; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('from_status'); ?></td>
            <td>

                <?php echo $function("from_status" , $change_datas['from_status']); ?>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('to_status'); ?></td>
            <td>
                <?php echo $function("to_status",$change_datas['to_status']); ?>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('status_days'); ?></td>
            <td>
                <input type="text" name="status_days" size="3" maxlength="3" value="<?php echo $change_datas['status_days'];?>"/>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('reminder'); ?></td>
            <td>
                <select name="reminder" >
                    <option value="1"<?php if ( 1 == $change_datas['reminder'] ):?> selected="selected" <?php endif; ?>><?php echo plugin_lang_get('yes'); ?></option>
                    <option value="0"<?php if ( 0 == $change_datas['reminder'] ):?> selected="selected" <?php endif; ?>><?php echo plugin_lang_get('no'); ?></option>
                </select>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('reminder_message'); ?></td>
            <td>
                <textarea name="reminder_message" cols="50" rows="5"><?php echo isset($change_datas['reminder_message']) ? $change_datas['reminder_message']: plugin_lang_get('before_change_status_message');?></textarea>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('reminder_days'); ?></td>
            <td>
                <input type="text" name="reminder_days" size="3" maxlength="3" value="<?php echo $change_datas['reminder_days'];?>"/>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('active'); ?></td>
            <td>
                <select name="active" >
                    <option value="1" <?php if ( 1 == $change_datas['active'] ):?> selected="selected" <?php endif; ?>><?php echo plugin_lang_get('yes'); ?></option>
                    <option value="0" <?php if ( 0 == $change_datas['active'] ):?> selected="selected" <?php endif; ?>><?php echo plugin_lang_get('no'); ?></option>
                </select>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="center" colspan="2">
                <?php if (isset($edit_id) ) : ?><input type="hidden" name="changestatus_id" value="<?php echo $edit_id;?>" /><?php endif; ?>
                <input type="submit" name="<?php if (isset($edit_id) && $edit_id != 0 ) : ?>submitEdit<?php else: ?>submitCreate<?php endif; ?>" value="<?php echo plugin_lang_get("create_change") ?>"/>
            </td>
        </tr>
    </table>
</form>
