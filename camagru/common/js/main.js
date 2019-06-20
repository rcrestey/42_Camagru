function form_action(form_id)
{
	// get form with given id
	form = document.getElementById(form_id);

	// get action form
	action = form.getAttribute('action');

	// get controller and action
	controller = action.split('.')[0];
	method = action.split('.')[1];

	// generate good url
	url = '/controllers/' + controller + '.php';
	
	// create hidden field who contrain the real action
	input = document.createElement('input');
	input.setAttribute('type','hidden');
	input.setAttribute('name','action');
	input.setAttribute('value',method);

	// add it in form
	form.appendChild(input);

	// set the good destionation of form
	form.setAttribute('action', url);

	//submit the form
	form.submit();
}