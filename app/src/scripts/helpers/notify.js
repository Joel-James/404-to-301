const {__} = wp.i18n
import {Store} from 'react-notifications-component'

const notify = (message = '', type = 'success') => {
	let title = ''
	switch (type) {
		case 'success':
			title = __('Success', '404-to-301')
			break
		case 'error':
			type = 'danger'
			title = __('Error', '404-to-301')
			break
		case 'info':
			title = __('Info', '404-to-301')
			break
		case 'warning':
			title = __('Warning', '404-to-301')
			break
		default:
			type = 'default'
			title = __('Note', '404-to-301')
			break

	}

	// Show notification.
	Store.addNotification({
		title: title,
		message: message,
		type: type,
		insert: 'bottom',
		container: 'bottom-left',
		animationIn: ['animate__animated', 'animate__fadeIn'],
		animationOut: ['animate__animated', 'animate__fadeOut'],
		dismiss: {
			duration: 5000,
			onScreen: true,
			pauseOnHover: true
		}
	})
}

export default notify
