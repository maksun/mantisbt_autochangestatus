<?php
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




$t_projects = project_cache_all();
?>
<p><?php plugin_lang_get('create_new_change_description'); ?></p>
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
                <?php echo print_status_option_list("from_status" , $change_datas['from_status']); ?>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('to_status'); ?></td>
            <td>
                <?php echo print_status_option_list("to_status",$change_datas['to_status']); ?>
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
                <textarea name="reminder_message" cols="50" rows="5"><?php echo $change_datas['reminder_message'];?></textarea>
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
                <input type="submit" name="<?php if (isset($edit_id) ) : ?>submitEdit<?php else: ?>submitCreate<?php endif; ?>" value="<?php echo plugin_lang_get("create_change") ?>"/>
            </td>
        </tr>
    </table>
</form>
