/**
 * Admin tab single menu item.
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

export default class Tab extends Component {
	static propTypes = {
		activeTab: PropTypes.string.isRequired,
		label: PropTypes.string.isRequired,
		onClick: PropTypes.func.isRequired,
	};

	onClick = () => {
		const { label, onClick } = this.props;
		onClick( label );
	};

	render() {
		const {
			onClick,
			props: {
				activeTab,
				label,
			},
		} = this;

		let className = 'nav-tab';

		if ( activeTab === label ) {
			className += ' nav-tab-active';
		}

		return (
			<a
				href={'#'}
				className={ className }
				onClick={ onClick }
			>
				{ label }
			</a>
		);
	}
}