/**
 * Menu tabs menu component.
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
import Tab from './tab';

export default class Tabs extends Component {
	static propTypes = {
		// Children is required.
		children: PropTypes.instanceOf( Array ).isRequired,
	};

	/**
	 * Construct the class.
	 *
	 * @param props
	 */
	constructor( props ) {
		super( props );

		// Default state.
		this.state = {
			activeTab: this.props.children[ 0 ].props.label,
		};
	}

	onClickTabItem = ( tab ) => {
		this.setState( { activeTab: tab } );
	};

	render() {
		const {
			onClickTabItem,
			props: {
				children,
			},
			state: {
				activeTab,
			}
		} = this;

		return (
			<div className="nav-tabs">
				<nav className="nav-tab-wrapper">
					{
						children.map(
							( child ) => {
								const { label } = child.props;

								return (
									<Tab
										activeTab={ activeTab }
										key={ label }
										label={ label }
										onClick={ onClickTabItem }
									/>
								);
							}
						)
					}
				</nav>
				<div className="tab-content">
					{
						children.map(
							( child ) => {
								if ( child.props.label !== activeTab ) {
									return undefined;
								}

								return child.props.children;
							}
						)
					}
				</div>
			</div>
		);
	}
}
