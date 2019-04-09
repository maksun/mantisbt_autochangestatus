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
#    2015-2019
#  http://www.h-hennes.fr/blog/

auth_reauthenticate();
access_ensure_global_level(config_get('manage_plugin_threshold'));

layout_page_header(plugin_lang_get('title'));
layout_page_begin();

print_manage_menu();

$query = "SELECT id,username
        FROM {user}
        ORDER BY username ASC";
$t_users = db_query($query);

$query_changes    = "SELECT p.name AS project_name, pacs.*
    FROM {plugin_autochangestatus} pacs
    LEFT JOIN {project} p ON p.id = pacs.project_id";
$t_changes        = db_query($query_changes);
$t_changes_number = db_num_rows($t_changes);

#Nom des statuts avec les traductions
$t_status_names = MantisEnum::getAssocArrayIndexedByValues( lang_get( 'status_enum_string' ) );
require_once(dirname(__FILE__).'/functions.php');

?>

<div class="col-md-12 col-xs-12">
    <div class="space-10"></div>
    <div class="form-container">
        <form action="<?php echo plugin_page( 'config_edit' )?>" method="post">
            <?php echo form_security_field( 'plugin_AutoChangeStatus_config_edit' ) ?>

            <div class="widget-box widget-color-blue2">
                <div class="widget-header widget-header-small">
                    <h4 class="widget-title lighter">
                        <?php echo plugin_lang_get('plugin_config_general_description') ?>
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed table-striped">
                                <tr>
                                    <th class="category">
                                        <?php echo plugin_lang_get('change_status_user'); ?>
                                    </th>
                                    <td>
                                        <select name="change_status_user">
                                            <option value="0"><?php echo plugin_lang_get('select_user'); ?></option>
                                            <?php while ($user = db_fetch_array($t_users)) : ?>
                                                <option value="<?php echo $user['id']; ?>" <?php if (plugin_config_get('change_status_user')== $user['id']):
                                                    ?> selected="selected"<?php endif; ?> >
                                                <?php echo $user['username']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                        <br>
                                        <span class="small"><?php echo plugin_lang_get('plugin_config_description'); ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="widget-toolbox padding-8 clearfix">
                        <input type="submit" class="btn btn-primary btn-white btn-round" value="<?php echo lang_get( 'change_configuration' )?>" />
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="space-10"></div>

    <div class="form-container">
        <form id="formatting-config-form" action="<?php echo plugin_page( 'config_edit' )?>" method="post">
            <?php echo form_security_field( 'plugin_AutoChangeStatus_config_edit' ) ?>

            <div class="widget-box widget-color-blue2">
                <div class="widget-header widget-header-small">
                    <h4 class="widget-title lighter">
                        <?php echo plugin_lang_get('plugin_changes_list'); ?>
                    </h4>
                </div>
                <div class="widget-body">
                    <div class="widget-main no-padding">
                        <div class="table-responsive">
                            <table class="table table-bordered table-condensed table-striped">
                                <thead>
                                    <tr>
                                        <th class="column-edit"> &nbsp; </th>
                                        <th><?php echo plugin_lang_get('project'); ?></th>
                                        <th><?php echo plugin_lang_get('from_status'); ?></th>
                                        <th><?php echo plugin_lang_get('active'); ?></th>
                                        <th><?php echo plugin_lang_get('status_days'); ?></th>
                                        <th><?php echo plugin_lang_get('to_status'); ?></th>
                                        <th><?php echo plugin_lang_get('reminder'); ?></th>
                                        <th><?php echo plugin_lang_get('reminder_days'); ?></th>
                                        <th><?php echo plugin_lang_get('reminder_message_private_short'); ?></th>
                                        <th><?php echo plugin_lang_get('reminder_message'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($t_changes_number > 0) : ?>
                                        <?php while ($change = db_fetch_array($t_changes)) : ?>
                                            <tr>
                                                <td class="column-edit center">
                                                    <a href="<?php echo plugin_page('changestatus');?>&changestatus_id=<?php echo $change['changestatus_id']; ?>">
                                                        <i class="fa fa-pencil bigger-130 padding-2 grey" title="<?php echo plugin_lang_get('edit'); ?>"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php
                                                        if( !empty( $change['project_name'] ) ) {
                                                            echo htmlentities($change['project_name']);
                                                        }
                                                        else
                                                        {
                                                            echo htmlentities(plugin_lang_get('all_projects'));
                                                        }
                                                    ?>

                                                </td>
                                                <td class="column-status">
                                                    <div class="align-left">
                                                        <?php
                                                        $status_label = html_get_status_css_class( $change['from_status'] );
                                                        echo '<i class="fa fa-square fa-status-box ' . $status_label . '"></i> ';
                                                        echo htmlentities($t_status_names[$change['from_status']]);
                                                        ?>
                                                    </div>
                                                </td>
                                                <?php if( $change['active'] == 1 ): ?>
                                                    <td class="center"><?php echo htmlentities(plugin_lang_get('yes')); ?></td>
                                                    <td class="center"><?php echo $change['status_days']; ?></td>
                                                    <td class="column-status">
                                                        <div class="align-left">
                                                            <?php
                                                            $status_label = html_get_status_css_class( $change['to_status'] );
                                                            echo '<i class="fa fa-square fa-status-box ' . $status_label . '"></i> ';
                                                            echo htmlentities($t_status_names[$change['to_status']]);
                                                            ?>
                                                        </div>
                                                    </td>
                                                <?php else: ?>
                                                    <td class="center"><?php echo htmlentities(plugin_lang_get('no')); ?></td>
                                                    <td class="center">&ndash;</td>
                                                    <td class="center">&ndash;</td>
                                                <?php endif; ?>
                                                <?php if( $change['reminder'] == 1 ): ?>
                                                    <td class="center"><?php echo htmlentities(plugin_lang_get('yes')); ?></td>
                                                    <td class="center"><?php echo $change['reminder_days']; ?></td>
                                                    <td class="center"><?php echo ($change['reminder_message_private'] == 1 ) ? plugin_lang_get('yes') : plugin_lang_get('no'); ?></td>
                                                    <td><?php echo nl2br(htmlentities(reminder_message_process( $change['reminder_message'], $change ))); ?></td>
                                                <?php else: ?>
                                                    <td class="center"><?php echo htmlentities(plugin_lang_get('no')); ?></td>
                                                    <td class="center">&ndash;</td>
                                                    <td class="center">&ndash;</td>
                                                    <td class="center">&ndash;</td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="center"><?php echo plugin_lang_get('no_update_changes_created'); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="widget-toolbox padding-8 clearfix">
                        <a href="<?php echo plugin_page('changestatus');?>" class="btn btn-primary btn-white btn-round"><?php echo plugin_lang_get("create_new_status_update") ?></a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
layout_page_end();
