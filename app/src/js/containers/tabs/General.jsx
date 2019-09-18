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

export default class General extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			settings: {
				name: '',
			},
			notice: false,
		};
	}

	render() {
		return (
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
		);
	}
}

General.propTypes = {
	wpObject: PropTypes.object
};