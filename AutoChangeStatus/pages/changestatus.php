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

if ( gpc_get('submitDelete',false)) {
    form_security_validate( 'plugin_AutoChangeStatus_changestatus_edit' );
    helper_ensure_confirmed( sprintf( plugin_lang_get( 'ensure_delete' ), $t_repo->name ), plugin_lang_get( 'delete_changestatus' ) );
    $query = "DELETE FROM {plugin_autochangestatus} WHERE changestatus_id=".db_param();
    db_query_bound($query,array(gpc_get_int('changestatus_id')));
    print_successful_redirect( plugin_page( 'config', true ) );
    form_security_purge( 'plugin_AutoChangeStatus_changestatus_edit' );
}

layout_page_header(plugin_lang_get('title'));
layout_page_begin();
print_manage_menu();


#Gestion de la soumission du formulaire (Création )
if ( gpc_get('submitCreate',false)) {
    form_security_validate( 'plugin_AutoChangeStatus_changestatus_edit' );
    $query = "INSERT INTO {plugin_autochangestatus}
        (`project_id`,`from_status`,`to_status`,`status_days`,`reminder`,`reminder_message`,`reminder_message_private`,`reminder_days`,`active`)
        VALUES ( ".db_param().",".db_param().",".db_param().",".db_param().",".db_param().",".db_param().",".db_param().",".db_param().",".db_param().")";

    db_query_bound($query,
        array(gpc_get_int('project_id'), gpc_get_int('from_status'), gpc_get_int('to_status'),
        gpc_get_int('status_days'), gpc_get_int('reminder'),
        gpc_get_string('reminder_message'),gpc_get_string('reminder_message_private'),gpc_get_int('reminder_days'), gpc_get_int('active'))
    );

    print_successful_redirect( plugin_page( 'config', true ) );
}


#Gestion de la soumission du formulaire (Mise à jour )
if ( gpc_get('submitEdit',false)) {
    form_security_validate( 'plugin_AutoChangeStatus_changestatus_edit' );
    $query = "UPDATE {plugin_autochangestatus} "
        . "SET `project_id` =".db_param().",`from_status`=".db_param().",`to_status`=".db_param().",`status_days`=".db_param().",`reminder`=".db_param().",`reminder_message`=".db_param().",`reminder_message_private`=".db_param().",`reminder_days`=".db_param().",`active`=".db_param().""
        ." WHERE changestatus_id=".db_param();

    db_query_bound($query,
        array(gpc_get_int('project_id'), gpc_get_int('from_status'), gpc_get_int('to_status'),
        gpc_get_int('status_days'), gpc_get_int('reminder'),
        gpc_get_string('reminder_message'),gpc_get_string('reminder_message_private'),gpc_get_int('reminder_days'), gpc_get_int('active'),gpc_get_int('changestatus_id'))
    );

    print_successful_redirect( plugin_page( 'config', true ) );
}

#Mise à jour récupération des données
if ( $edit_id = gpc_get_int('changestatus_id' , false) ) {
    $change_query = db_query("SELECT * FROM {plugin_autochangestatus} WHERE changestatus_id=".$edit_id);
    $change_datas = db_fetch_array($change_query);
}

#Bug rencontrés avec certains workflow
include_once(dirname(__FILE__).'/functions.php');
$function = 'print_status_option_list_plugin';

$t_projects = project_cache_all();
?>
<div class="col-md-12 col-xs-12">
    <div class="space-10"></div>
    <div class="form-container">
        <form action="<?php echo plugin_page('changestatus') ?>" method="post">
            <?php echo form_security_field( 'plugin_AutoChangeStatus_changestatus_edit' ) ?>

            <div class="widget-box widget-color-blue2">
                <div class="widget-header widget-header-small">
                    <h4 class="widget-title lighter">
                        <?php echo plugin_lang_get('create_new_change_description') ?>
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed table-striped">
                                <tr>
                                    <th class="category"><?php echo plugin_lang_get('project'); ?></th>
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
                                <tr>
                                    <th class="category"><?php echo plugin_lang_get('from_status'); ?></th>
                                    <td>
                                        <?php echo $function("from_status" , $change_datas['from_status']); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="category"><?php echo plugin_lang_get('to_status'); ?></th>
                                    <td>
                                        <?php echo $function("to_status",$change_datas['to_status']); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="category">
                                        <?php echo plugin_lang_get('status_days'); ?>
                                        <br>
                                        <span class="small"><?php echo plugin_lang_get('status_days_description'); ?></span>
                                    </th>
                                    <td>
                                        <input type="text" class="center" name="status_days" size="3" maxlength="3" value="<?php echo $change_datas['status_days'];?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="category"><?php echo plugin_lang_get('reminder'); ?></th>
                                    <td>
                                        <select name="reminder" >
                                            <option value="1"<?php if ( 1 == $change_datas['reminder'] ):?> selected="selected" <?php endif; ?>><?php echo plugin_lang_get('yes'); ?></option>
                                            <option value="0"<?php if ( 0 == $change_datas['reminder'] ):?> selected="selected" <?php endif; ?>><?php echo plugin_lang_get('no'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="category"><?php echo plugin_lang_get('reminder_message'); ?></th>
                                    <td>
                                        <textarea name="reminder_message" cols="60" rows="10"><?php echo isset($change_datas['reminder_message']) ? $change_datas['reminder_message']: plugin_lang_get('before_change_status_message');?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="category"><?php echo plugin_lang_get('reminder_message_private'); ?></th>
                                    <td>
                                        <label for="reminder_message_private_0">
                                            <input type="radio" class="ace" name="reminder_message_private" id="reminder_message_private_0" value="0" <?php echo( ON != $change_datas['reminder_message_private'] ) ? 'checked="checked" ' : ''?>/>
                                            <span class="lbl padding-6"><?php echo lang_get('public'); ?></span>
                                        </label>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <label for="reminder_message_private_1">
                                            <input type="radio" class="ace" name="reminder_message_private" id="reminder_message_private_1" value="1" <?php echo( ON == $change_datas['reminder_message_private'] ) ? 'checked="checked" ' : ''?>/>
                                            <span class="lbl padding-6"><?php echo lang_get('private'); ?></span>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="category">
                                        <?php echo plugin_lang_get('reminder_days'); ?>
                                        <br>
                                        <span class="small"><?php echo plugin_lang_get('reminder_days_description'); ?></span>
                                    </th>
                                    <td>
                                        <input type="text" class="center" name="reminder_days" size="3" maxlength="3" value="<?php echo $change_datas['reminder_days'];?>"/>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="category"><?php echo plugin_lang_get('active'); ?></th>
                                    <td>
                                        <select name="active" >
                                            <option value="1" <?php if ( 1 == $change_datas['active'] ):?> selected="selected" <?php endif; ?>><?php echo plugin_lang_get('yes'); ?></option>
                                            <option value="0" <?php if ( 0 == $change_datas['active'] ):?> selected="selected" <?php endif; ?>><?php echo plugin_lang_get('no'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="widget-toolbox padding-8 clearfix">
                        <?php if (isset($edit_id) && $edit_id != 0): ?>
                            <input type="hidden" name="changestatus_id" value="<?php echo $edit_id;?>" />
                            <input type="submit" class="btn btn-primary btn-white btn-round" name="submitEdit" value="<?php echo plugin_lang_get( 'change' )?>" />
                            <input type="submit" class="btn btn-danger btn-white btn-round" name="submitDelete" value="<?php echo plugin_lang_get( 'delete' )?>" />
                        <?php else: ?>
                            <input type="submit" class="btn btn-primary btn-white btn-round" name="submitCreate" value="<?php echo plugin_lang_get( 'create' )?>" />
                        <?php endif; ?>
                        <a class="btn btn-default btn-white btn-round" href="<?php echo plugin_page('config') ?>"><?php echo plugin_lang_get('back') ?></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
layout_page_end();
