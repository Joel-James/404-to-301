<template>
	<component :is="type">
		<slot></slot>
	</component>
</template>

<script>
import Info from './notifications/info'
import Error from './notifications/error'
import Success from './notifications/success'
import Warning from './notifications/warning'

export default {
	name: 'UiNotification',

	components: {
		Info,
		Error,
		Success,
		Warning,
	},

	props: {
		type: {
			type: String,
			default: 'Info',
		},
	},
}
</script>
