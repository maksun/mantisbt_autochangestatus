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

# Surcharge de la fonction print_status_option_list
function print_status_option_list_plugin( $p_select_label, $p_current_value = 0, $p_allow_close = false, $p_project_id = ALL_PROJECTS ) {
	$t_current_auth = access_get_project_level( $p_project_id );

        #Changement de la fonction de récupération des statuts
	$t_enum_list = get_status_option_list_plugin( $t_current_auth, $p_current_value, true, $p_allow_close, $p_project_id );

	if( count( $t_enum_list ) > 1 ) {

		# resort the list into ascending order
		ksort( $t_enum_list );
		reset( $t_enum_list );
		echo '<select ', helper_get_tab_index(), ' name="' . $p_select_label . '">';
		foreach( $t_enum_list as $key => $val ) {

                    #On ne veut pas afficher la valeur @0@
                    if ( $val == '@0@')
                        continue;

			echo '<option value="' . $key . '"';
			check_selected( $key, $p_current_value,false );#fix 1.3.0
			echo '>' . $val . '</option>';
		}
		echo '</select>';
	} else if ( count( $t_enum_list ) == 1 ) {
		echo array_pop( $t_enum_list );
	} else {
		echo MantisEnum::getLabel( lang_get( 'status_enum_string' ), $p_current_value );
	}
}

#Surcharge de la fonction get_status_option_list
function get_status_option_list_plugin( $p_user_auth = 0, $p_current_value = 0, $p_show_current = true, $p_add_close = false, $p_project_id = ALL_PROJECTS ) {
	$t_config_var_value = config_get( 'status_enum_string', null, null, $p_project_id );
	$t_enum_workflow = config_get( 'status_enum_workflow', null, null, $p_project_id );


	$t_enum_values = MantisEnum::getValues( $t_config_var_value );
	$t_enum_list = array();

	foreach ( $t_enum_values as $t_enum_value ) {
		if (   ( $p_show_current || $p_current_value != $t_enum_value )
			&& access_compare_level( $p_user_auth, access_get_status_threshold( $t_enum_value, $p_project_id ) )
		) {
			$t_enum_list[$t_enum_value] = get_enum_element( 'status', $t_enum_value );
		}
	}

	if ( $p_show_current ) {
		$t_enum_list[$p_current_value] = get_enum_element( 'status', $p_current_value );
	}

	if ( $p_add_close && access_compare_level( $p_current_value, config_get( 'bug_resolved_status_threshold', null, null, $p_project_id ) ) ) {
		$t_closed = config_get( 'bug_closed_status_threshold', null, null, $p_project_id );
		if( $p_show_current || $p_current_value != $t_closed ) {
			$t_enum_list[$t_closed] = get_enum_element( 'status', $t_closed );
		}
	}

	return $t_enum_list;
}
