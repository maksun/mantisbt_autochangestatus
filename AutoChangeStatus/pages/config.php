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

$t_user_table = db_get_table('mantis_user_table');
$query        = "SELECT id,username
        FROM $t_user_table
        ORDER BY username ASC";

$t_users = db_query($query);

#$t_changes_table  = db_get_table('mantis_autochange_status');
$query_changes    = "SELECT * FROM mantis_autochange_status";
$t_changes        = db_query($query_changes);
$t_changes_number = db_num_rows($t_changes);

#Nom des statuts avec les traductions
$t_status_names = MantisEnum::getAssocArrayIndexedByValues( lang_get( 'status_enum_string' ) );

?>
<br />
<h2><?php echo plugin_lang_get('plugin_config_general_description'); ?></h2>
<p><?php echo plugin_lang_get('plugin_config_description'); ?></p>
<form action="<?php echo plugin_page('config_edit') ?>" method="post">
    <table>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="category"><?php echo plugin_lang_get('change_status_user'); ?></td>
            <td>
                <select name="change_status_user">
                    <option value="0"><?php echo plugin_lang_get('select_user'); ?></option>
                    <?php while ($user             = db_fetch_array($t_users)) : ?>
                        <option value="<?php echo $user['id']; ?>" <?php if (plugin_config_get('change_status_user')== $user['id']):
                            ?> selected="selected"<?php endif; ?> >
                        <?php echo $user['username']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </td>
        </tr>
        <tr <?php echo helper_alternate_class() ?>>
            <td class="center" colspan="2"><input type="submit" value="<?php echo plugin_lang_get("config_action_update") ?>"/></td>
        </tr>
    </table>
</form>

<h2><?php echo plugin_lang_get('plugin_changes_list'); ?></h2>
<table class="width100" cellspacing="1">
    <tr class="row-category" >
        <td><?php echo plugin_lang_get('project'); ?></td>
        <td><?php echo plugin_lang_get('from_status'); ?></td>
        <td><?php echo plugin_lang_get('to_status'); ?></td>
        <td><?php echo plugin_lang_get('status_days'); ?></td>
        <td><?php echo plugin_lang_get('reminder'); ?></td>
        <td><?php echo plugin_lang_get('reminder_message'); ?></td>
        <td><?php echo plugin_lang_get('reminder_days'); ?></td>
        <td><?php echo plugin_lang_get('active'); ?></td>
        <td><?php echo plugin_lang_get('edit'); ?></td>
    </tr>
    <?php if ($t_changes_number > 0) : ?>
    <?php while ($change = db_fetch_array($t_changes)) : ?>
            <tr  <?php echo helper_alternate_class() ?>>
                <td><?php echo $change['project_id']; ?></td>
                <td><?php echo $t_status_names[$change['from_status']]; ?></td>
                <td><?php echo $t_status_names[$change['to_status']]; ?></td>
                <td><?php echo $change['status_days']; ?></td>
                <td><?php echo ($change['reminder'] == 1 ) ? plugin_lang_get('yes') : plugin_lang_get('no'); ?></td>
                <td><?php echo $change['reminder_message']; ?></td>
                <td><?php echo $change['reminder_days']; ?></td>
                <td><?php echo ($change['active'] == 1 ) ? plugin_lang_get('yes') : plugin_lang_get('no'); ?></td>
                <td><a href="<?php echo plugin_page('changestatus');?>&changestatus_id=<?php echo $change['changestatus_id']; ?>"><?php echo plugin_lang_get('edit'); ?></a>
            </tr>
        <?php endwhile; ?>
<?php else: ?>
        <tr class="row-1">
            <td colspan="9" style="text-align:center;"><?php echo plugin_lang_get('no_update_changes_created'); ?></td>
        </tr>
<?php endif; ?>
</table>

<p><a href="<?php echo plugin_page('changestatus');?>"><?php echo plugin_lang_get('create_new_status_update');?></a></p>
