import React from "react";

export default class FPF_Button_Add extends React.Component {

	constructor( props ) {
		super( props );
		this.handleClick = this.handleClick.bind(this);
	}

	handleClick( e ) {
		e.preventDefault();
		var key = "fpf_"+Math.floor((Math.random() * 10000000) + 1);
		var data = {
			id: key,
			title: fpf_admin.new_field_title,
			type: 'text',
			max_length: '',
			required: false,
			placeholder: '',
			css_class: '',
			price: '',
			price_type: fpf_price_type_options[0].value,
			date_format: 'dd.mm.yy',
			days_before: '',
			days_after: '',
			logic: false,
			logic_operator: 'or',
			display: true,
		};
		this.props.addField(data);
	}

	render() {
		return (
			<button className="fpf-button-add button button-primary" onClick={this.handleClick}>
				{fpf_admin.add_field_label}
			</button>
		)
	}
}
