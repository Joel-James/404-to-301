<template>
	<div :class="noticeClass">
		<div class="sui-notice-content">
			<div class="sui-notice-message">
				<i
					class="sui-notice-icon sui-icon-info sui-md"
					aria-hidden="true"
				></i>
				<!-- Notice content -->
				<slot></slot>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'SuiNotice',

	props: {
		type: {
			type: String,
			default: 'success',
		},
	},

	mounted() {
		SUI.notice()
	},

	computed: {
		/**
		 * Get notice classes based of different conditions.
		 *
		 * @since 3.2.3
		 *
		 * @return {*}
		 */
		noticeClass() {
			return {
				'sui-notice': true,
				'sui-notice-info': this.type === 'info',
				'sui-notice-error': this.type === 'error',
				'sui-notice-success': this.type === 'success',
				'sui-notice-warning': this.type === 'warning',
			}
		},
	},
}
</script>
