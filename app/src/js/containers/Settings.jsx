import React, {Component} from 'react';
import PropTypes from 'prop-types';

import fetchWP from '../utils/fetchWP';

export default class Settings extends Component {
	constructor(props) {
		super(props);

		this.state = {
			name: ''
		};

		this.fetchWP = new fetchWP({
			restURL: this.props.wpObject.api_url,
			restNonce: this.props.wpObject.api_nonce,
		});

		this.getSetting();
	}

	getSetting = () => {
		this.fetchWP.get('settings')
		.then(
			(json) => this.setState({
				name: json.value
			}),
		);
	};

	updateSetting = () => {
		this.fetchWP.post('settings', {value: this.state.name})
		.then(
			(json) => this.processOkResponse(json, 'saved'),
		);
	}

	processOkResponse = (json, action) => {
		if (json.success) {
			this.setState({
				name: json.value
			});
		}
	}

	updateInput = (event) => {
		this.setState({
			name: event.target.value,
		});
	}

	handleSave = (event) => {
		event.preventDefault();
		this.updateSetting();
	}

	render() {
		return (
			<form className="wrap">
				<h1>404 to 301</h1>
				<table className="form-table">
					<tbody>
					<tr>
						<th>
							<label>Example Setting:</label>
						</th>
						<td>
							<input type="text" value={this.state.name} onChange={this.updateInput}/>
						</td>
					</tr>
					<tr>
						<td colSpan={2}>
							<button id="save" className="button button-primary" onClick={this.handleSave}>Save Settings</button>
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