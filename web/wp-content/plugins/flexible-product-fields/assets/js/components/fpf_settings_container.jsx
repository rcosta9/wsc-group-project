import React, {Component} from 'react';
import FPF_Settings_Field from './fpf_settings_field.jsx';

export default class FPF_Settings_Container extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			settings: [ ]
		}
	}

	render() {
		return (
			<div className="fpf-fields-settings">
				<div className="fpf-inputs">
					<FPF_Settings_Field
						assign_to={fpf_settings.assign_to}
						section={fpf_settings.section}
						products={fpf_settings.products}
						categories={fpf_settings.categories}
						menu_order={fpf_settings.menu_order}
						ref="settings_group"
					/>
				</div>
			</div>
		);
	}
}
