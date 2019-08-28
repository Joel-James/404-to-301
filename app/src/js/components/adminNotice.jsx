import React from 'react';
import PropTypes from 'prop-types';

/**
 * WordPress admin notice component.
 *
 * The returned markup uses the standard WordPress notice classes, so no extra styling required.
 * The resulting notice class and message will depend on the contents of the 'notice' prop object,
 * passed down from Admin.jsx.
 *
 * @since 4.0.0
 */
export default class adminNotice extends React.Component {

	/**
	 * As soon as the component has mounted.
	 *
	 * @since 4.0.0
	 */
	componentDidMount() {
		// If the duration prop is greater than zero.
		if ( this.props.duration > 0 ) {
			// Then, after the set duration has passed, run the onDismissClick function
			// that has been passed down as a prop from our Admin.jsx container.
			this.dismissTimeout = window.setTimeout(
				this.props.onDismissClick,
				this.props.duration
			);
		}
	}

	/**
	 * When the component is about to removed from the DOM.
	 *
	 * @since 4.0.0
	 */
	componentWillUnmount() {
		// If this.dismissTimeout was set in componentDidMount().
		if ( this.dismissTimeout ) {
			// Reset the timer when the notice is dismissed (and therefore 'unmounted').
			window.clearTimeout( this.dismissTimeout );
		}
	}

	/**
	 * Render the component to the view.
	 *
	 * @since 4.0.0
	 */
	render() {
		// Define dismiss as an empty variable.
		let dismiss;

		// if our showDismiss prop is set to true.
		if ( this.props.showDismiss ) {
			// Set the dismiss variable to contain our dismiss button markup.
			dismiss = (
				<span
					tabIndex="0"
					className="notice_dismiss"
					onClick={ this.props.onDismissClick }
				>
					<span className="dashicons dashicons-dismiss" />
					<span className="screen-reader-text">Dismiss</span>
				</span>
			);
		}

		// Return the markup.
		return (
			<div className={ `notice is-dismissible notice-${ this.props.notice.type }` }>
				<p><strong>{ this.props.notice.message }</strong></p>
				{ dismiss }
			</div>
		);
	}
}

// Set default values for some of our props.
adminNotice.defaultProps = {
	duration: 4000,
	showDismiss: true,
	onDismissClick: null,
};

// And define our propTypes.
adminNotice.propTypes = {
	duration: PropTypes.number,
	showDismiss: PropTypes.bool,
	onDismissClick: PropTypes.func,
	notice: PropTypes.oneOfType( [
		PropTypes.bool,
		PropTypes.shape( {
			type: PropTypes.string,
			message: PropTypes.string
		} )
	] ),
};