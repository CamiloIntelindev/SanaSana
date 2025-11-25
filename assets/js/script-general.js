let notifyController = null
class NotifyController{

	constructor(){
		this.notify = new Notyf({position:{x:'right',y:'top'}, duration: 3000, dismisable: true });
	}

	showMessage(message, error = true) {
		error ?  this.notify.error(message): this.notify.success(message)
	}	
}

jQuery(document).ready(function($) {
	notifyController = new NotifyController();
});
