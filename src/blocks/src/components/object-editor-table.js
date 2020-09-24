const ObjectEditorTableRow = props => {
	const {
		mObject
	} = props;
	
	const {
		link,
		edit_link         : editLink,
		post_title        : postTitle,
		post_status_label : postStatus
	} = mObject;

	return (
		<tr>
			<td>{ postTitle }</td>
			<td><a href = { editLink }>Edit</a></td>
			<td><a href = { link }>View</a></td>
			<td>{ postStatus }</td>
		</tr>
	);
}

const ObjectEditorTable = props => {
	const {
		mObjects
	} = props;

	const mObjectRows = mObjects.map( mObject =>
		<ObjectEditorTableRow mObject = { mObject } /> );

	return (
		<table class='wp-list-table widefat'>
			{ mObjectRows }
		</table>
	);
}

export default ObjectEditorTable;