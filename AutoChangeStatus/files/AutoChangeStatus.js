$(document).ready( function() {
	$( "#acs-active" )
		.change( AutoChangeStatusPlugin.toggleActiveRows )
		.change();
	$( "#acs-reminder" )
		.change( AutoChangeStatusPlugin.toggleReminderRows )
		.change();
});

var AutoChangeStatusPlugin = {
	toggleActiveRows: function()
	{
		var _active = $( "#acs-active" ).val();
		var $visibleActiveRows = $( ".visible-active" );
		if( _active == 1 )
		{
			$visibleActiveRows.show();
		}
		else
		{
			$visibleActiveRows.hide();
		}
	},
	toggleReminderRows: function()
	{
		var _reminder = $( "#acs-reminder" ).val();
		var $visibleReminderRows = $( ".visible-reminder" );
		if( _reminder == 1 )
		{
			$visibleReminderRows.show();
		}
		else
		{
			$visibleReminderRows.hide();
		}
	}
};
