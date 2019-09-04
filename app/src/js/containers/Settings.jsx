/**
 * Admin settings page container.
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

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import fetchWP from '../utils/fetchWP';
import Notice from '../components/notice';
import Tabs from '../components/tabs';

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
			notice = <Notice notice={ this.state.notice } onDismissClick={ this.clearNotice }/>
		}

		return (
			<div className={ 'dd404-wrap' }>
				{ notice }
				<Tabs>
					<div label="Tab 1">
						<form>
							<table className="form-table">
								<tbody>
								<tr>
									<th>
										<label>Tab 1:</label>
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
					</div>
					<div label="Tab 2">
						<form>
							<table className="form-table">
								<tbody>
								<tr>
									<th>
										<label>Tab 2:</label>
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
					</div>
				</Tabs>
			</div>
		);
	}
}

Settings.propTypes = {
	wpObject: PropTypes.object
};