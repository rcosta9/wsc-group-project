import React from "react";
import {DragHandle} from "./drag_handle.jsx";

export default class FPF_Rule extends React.Component {

	constructor(props) {
		super( props );
		let fields = [];
		let values = [];
		jQuery.each(fpf_settings['fields'], function( index, value) {
			if ( value.type == 'select' || value.type == 'radio' ) {
				fields.push( value );
				if ( props.value.field == value.id ) {
					jQuery.each(value.options, function (option_index, option_value) {
						values.push(option_value);
					});
				}
			}
			if ( value.type == 'checkbox' ) {
				fields.push( value );
				if ( props.value.field == value.id ) {
					values.push({value: 'checked', label: fpf_admin.checked_label});
					values.push({value: 'unchecked', label: fpf_admin.unchecked_label});
				}
			}
		});
		this.state = {
			data: props.value,
			fields: fields,
			field_values: values,
		};

		this.onChangeField = this.onChangeField.bind(this);
		this.onChangeCompare = this.onChangeCompare.bind(this);
		this.onChangeFieldValue = this.onChangeFieldValue.bind(this);
	}

	onChangeField(event) {
		let data2 = this.state.data;
		data2.field = event.target.value;
		data2.field_value = '';
		let fields = [];
		let values = [];
		jQuery.each(fpf_settings['fields'], function( index, value) {
			if ( value.type == 'select' || value.type == 'radio' ) {
				fields.push( value );
				if ( data2.field == value.id ) {
					jQuery.each(value.options, function (option_index, option_value) {
						values.push(option_value);
					});
				}
			}
			if ( value.type == 'checkbox' ) {
				fields.push( value );
				if ( data2.field == value.id ) {
					values.push( { value: 'checked', label: fpf_admin.checked_label } );
					values.push( { value: 'unchecked', label: fpf_admin.unchecked_label } );
				}
			}
		});
		this.setState( {
			data: data2,
			fields: fields,
			field_values: values,
		} );
		this.forceUpdate();
	}

	onChangeCompare(event) {
		let data2 = this.state.data;
		data2.compare = event.target.value;
		this.setState( { data: data2 } );
	}

	onChangeFieldValue(event) {
		let data2 = this.state.data;
		data2.field_value = event.target.value;
		this.setState( { data: data2 } );
	}


	render() {
		return (
			<tr>
				<td className="fpf-row-handle">
					<DragHandle/>
				</td>
				<td>
					<select name="field_logic_field" onChange={this.onChangeField} value={this.state.data.field}>
						<option value="">{fpf_admin.logic_select_field}</option>
						{this.state.fields.map((value, index) =>
							<option key={index} value={value.id}>{value.title}</option>
						)}
					</select>
				</td>
				<td>
					<select name="field_logic_compare" onChange={this.onChangeCompare} value={this.state.data.compare}>
						<option value="is">{fpf_admin.logic_compare_is}</option>
						<option value="is_not">{fpf_admin.logic_compare_is_not}</option>
					</select>
				</td>
				<td>
					<select name="field_logic_field_value" onChange={this.onChangeFieldValue} value={this.state.data.field_value}>
						<option value="">{fpf_admin.logic_select_field_value}</option>
						{this.state.field_values.map((value, index) =>
							<option key={index} value={value.value}>{value.label}</option>
						)}
					</select>
				</td>
				<td className="fpf-row-handle">
					<a className="fpf-rule-delete dashicons dashicons-trash" onClick={() => this.props.onRemove(this.state.data.id)}> </a>
				</td>
			</tr>
		)
	}

}
