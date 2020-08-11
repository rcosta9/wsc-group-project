import React from "react";
import FPF_Button_Add from "./fpf_button_add.jsx";
import FPF_Fields from "./fpf_fields.jsx";

export default class FPF_Fields_Container extends React.Component {

	constructor(props) {
		super(props);
		this.state = {
			fields: fpf_settings.fields,
			assign_to: fpf_settings.assign_to.value
		};
		this.addField = this.addField.bind(this);
		this.changeAssignTo = this.changeAssignTo.bind(this);
		Fields = this;
	}

	changeAssignTo( assign_to ) {
		this.setState(
			{ assign_to: assign_to }
		);
	}

	addField( data ) {
		let fields = this.state.fields;
		fields.push( data );
		this.setState(
			{ fields: fields }
		);
	}

	render() {
		return (
			<div className="fpf-fields-set">
				<div>
					<FPF_Fields fields={this.state.fields} ref="fields"/>
					<div className="fpf-footer">
						< FPF_Button_Add addField={this.addField}/>
					</div>
				</div>
			</div>
		);
	}
}
