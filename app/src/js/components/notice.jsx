/**
 * WordPress admin notice component.
 *
 * The returned markup uses the standard WordPress notice classes, so no extra styling required.
 * The resulting notice class and message will depend on the contents of the 'notice' prop object,
 * passed down from Admin.jsx.
 *
 * 404 to 301, Copyright 2019 Duck Dev.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

import React from 'react';
import PropTypes from 'prop-types';

export default class Notice extends React.Component {

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
		// Return the markup.
		return (
			<div className={ `notice is-dismissible notice-${ this.props.notice.type }` }>
				<p><strong>{ this.props.notice.message }</strong></p>
			</div>
		);
	}
}

// Set default values for some of our props.
Notice.defaultProps = {
	duration: 3000,
};

// And define our propTypes.
Notice.propTypes = {
	duration: PropTypes.number,
	notice: PropTypes.oneOfType( [
		PropTypes.bool,
		PropTypes.shape( {
			type: PropTypes.string,
			message: PropTypes.string
		} )
	] ),
};