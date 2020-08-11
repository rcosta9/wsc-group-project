import React from "react";
import {DragHandle} from "./drag_handle.jsx";

export default class FPF_Option extends React.Component {

	constructor(props) {
		super( props );
		this.state = {
			data: props.value,
		};

		this.onChangeValue = this.onChangeValue.bind(this);
		this.onChangeLabel = this.onChangeLabel.bind(this);
		this.onChangePriceType = this.onChangePriceType.bind(this);
		this.onChangePrice = this.onChangePrice.bind(this);
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

	onChangePriceType(event) {
		let data2 = this.state.data;
		data2.price_type = event.target.value;
		this.setState( { data: data2 } );
	}

	onChangePriceType2(val) {
		let data2 = this.state.data;
		data2.price_type = val.value;
		this.setState( { data: data2 } );
	}

	onChangePrice(event) {
		let data2 = this.state.data;
		data2.price = event.target.value;
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
					<select name="price_type" onChange={this.onChangePriceType} value={this.state.data.price_type}>
						{
							fpf_price_type_options.map(function (item) {
								return <option key={item.value} value={item.value}>{item.label}</option>;
							})
						}
					</select>
				</td>
				<td>
					<input type="number" className="fpf-field-price" name="field_price" value={this.state.data.price} onChange={this.onChangePrice} step="0.01" />
				</td>
				<td className="fpf-row-handle">
					<a className="fpf-option-delete dashicons dashicons-trash" onClick={() => this.props.onRemove(this.state.data.id)}> </a>
				</td>
			</tr>
		)
	}

}
