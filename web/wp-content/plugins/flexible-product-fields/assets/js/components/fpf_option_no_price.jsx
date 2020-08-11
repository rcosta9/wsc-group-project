import React from "react";
import {DragHandle} from "./drag_handle.jsx";

export default class FPF_Option_No_Price extends React.Component {

	constructor(props) {
		super( props );
		this.state = {
			data: props.value,
		};

		this.onChangeValue = this.onChangeValue.bind(this);
		this.onChangeLabel = this.onChangeLabel.bind(this);
	}

	onChangeValue(event) {
		let data2 = this.state.data;
		data2.value = event.target.value;
		this.setState( { data: data2 } );
	}

	onChangeLabel(event) {
		let data2 = this.state.data;
		data2.label = event.target.value;
		this.setState( { data: data2 } );
	}

	render() {
		return (
			<tr>
				<td className="fpf-row-handle">
					<DragHandle/>
				</td>
				<td>
					<input type="text" className="fpf-field-option-value" name="option_value" value={this.state.data.value} onChange={this.onChangeValue}/>
				</td>
				<td>
					<input type="text" className="fpf-field-option-label" name="option_label" value={this.state.data.label} onChange={this.onChangeLabel}/>
				</td>
				<td>
					{fpf_admin.price_adv} <a href={fpf_admin.price_adv_link}>{fpf_admin.price_adv_link_text}</a>
				</td>
				<td className="fpf-row-handle">
					<a className="fpf-option-delete dashicons dashicons-trash" onClick={() => this.props.onRemove(this.state.data.id)}> </a>
				</td>
			</tr>
		)
	}

}
