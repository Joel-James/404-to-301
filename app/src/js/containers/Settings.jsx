import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from '../utils/fetchWP';
import adminNotice from "../components/adminNotice";

/**
 * Admin settings page container.
 *
 * @since 4.0.0
 */
export default class Settings extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			settings: {
				name: '',
			},
			notice: false,
		};

		// Create new instance of fetchWP.
		this.fetchWP = new fetchWP( {
			restURL: this.props.wpObject.api_url,
			restNonce: this.props.wpObject.api_nonce,
		} );

		// Get settings.
		this.getSetting();
	}

	/**
	 * Get settings from database.
	 *
	 * @since 4.0.0
	 */
	getSetting = () => {
		this.fetchWP.get( 'settings' ).then(
			( json ) => this.setState( {
				settings: json.settings
			} ),
		);
	};

	/**
	 * Update settings value.
	 *
	 * @since 4.0.0
	 */
	updateSetting = () => {
		this.fetchWP.post( 'settings', {
			value: this.state.settings
		} ).then(
			( json ) => this.processResponse( json, 'saved' ),
		);
	};

	/**
	 * Process the response from ajax.
	 *
	 * @param {string} json
	 * @param action
	 *
	 * @since 4.0.0
	 */
	processResponse = ( json, action ) => {
		if ( json.success ) {
			this.setState( {
				notice: {
					type: 'success',
					message: 'Setting updated successfully.',
				}
			} );
		} else {
			this.setState( {
				notice: {
					type: 'error',
					message: 'Oops! Something went wrong.',
				}
			} );
		}
	};

	/**
	 * Update input values from state.
	 *
	 * @param event
	 *
	 * @since 4.0.0
	 */
	updateInput = ( event ) => {
		this.setState( {
			settings: {
				name: event.target.value
			},
		} );
	};

	/**
	 * Handle form submit.
	 *
	 * @param event
	 *
	 * @since 4.0.0
	 */
	handleSave = ( event ) => {
		// Do not redirect.
		event.preventDefault();

		// Update values.
		this.updateSetting();
	};

	/**
	 * Hide notice element from DOM.
	 *
	 * @since 4.0.0
	 */
	clearNotice = () => {
		this.setState( {
			notice: false,
		} );
	};

	render() {
		let notice;

		// If notice is visible.
		if ( this.state.notice ) {
			notice = <adminNotice notice={ this.state.notice } onDismissClick={ this.clearNotice }/>
		}

		return (
			<form className="wrap">
				<h1>404 to 301</h1>
				{ notice }
				<table className="form-table">
					<tbody>
					<tr>
						<th>
							<label>Example Setting:</label>
						</th>
						<td>
							<input type="text"
								   name={ 'name' }
								   value={ this.state.settings.name }
								   onChange={ this.updateInput }
							/>
						</td>
					</tr>
					<tr>
						<td colSpan={ 2 }>
							<button
								id="save"
								className="button button-primary"
								onClick={ this.handleSave }
							>
								Save Settings
							</button>
						</td>
					</tr>
					</tbody>
				</table>
			</form>
		);
	}
}

Settings.propTypes = {
	wpObject: PropTypes.object
};