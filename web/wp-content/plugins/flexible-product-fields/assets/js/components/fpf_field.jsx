import React from "react";
import {SortableListOptions, SortableListOptionsNoPrice, SortableListRules} from "./sortable.jsx";
import {DragHandle} from "./drag_handle.jsx";
import {arrayMove} from 'react-sortable-hoc';

export default class FPF_Field extends React.Component {

	constructor(props) {
		super( props );
		this.state = {
			data: props.data,
			i: props.i,
			key: props.key,
			other: props.other,
			id: props.data.id,
		};
		if ( typeof this.state.data.options == 'undefined' ) {
			this.state.data.options = [];
		}
		if ( typeof this.state.data.logic_rules == 'undefined' ) {
			this.state.data.logic_rules = [];
		}
		this.onChangeTitle = this.onChangeTitle.bind(this);
		this.onChangeType = this.onChangeType.bind(this);
		this.onChangeRequired = this.onChangeRequired.bind(this);
		this.onChangeMaxLength = this.onChangeMaxLength.bind(this);
		this.onChangeCssClass = this.onChangeCssClass.bind(this);
		this.onChangePlaceholder = this.onChangePlaceholder.bind(this);
		this.onChangeValue = this.onChangeValue.bind(this);
		this.onChangePriceType = this.onChangePriceType.bind(this);
		this.onChangePrice = this.onChangePrice.bind(this);
		this.onChangeDateFormat = this.onChangeDateFormat.bind(this);
		this.onChangeDaysBefore = this.onChangeDaysBefore.bind(this);
		this.onChangeDaysAfter = this.onChangeDaysAfter.bind(this);

		this.onChangeLogic = this.onChangeLogic.bind(this);
		this.onChangeLogicOperator = this.onChangeLogicOperator.bind(this);

		this.onClickToggleDisplay = this.onClickToggleDisplay.bind(this);
		this.handleMouseEnter = this.handleMouseEnter.bind(this);
		this.handleMouseLeave = this.handleMouseLeave.bind(this);

		this.handleAddOption = this.handleAddOption.bind(this);
		this.onRemoveOption = this.onRemoveOption.bind(this);

		this.handleAddRule = this.handleAddRule.bind(this);
		this.onRemoveRule = this.onRemoveRule.bind(this);

	}

	onChangeTitle(event) {
		let data2 = this.state.data;
		data2.title = event.target.value;
		this.setState( { data: data2 } );
	}

	onChangeType(event) {
		let data2 = this.state.data;
		data2.type = event.target.value;
		this.setState( { data: data2 } );
	}

	onChangeType2(val) {
		let data2 = this.state.data;
		data2.type = val.value;
		this.setState( { data: data2 } );
	}

	onChangeRequired(event) {
		let data2 = this.state.data;
		data2.required = !data2.required;
		this.setState( { data: data2 } );
	}

	onChangeMaxLength(event) {
		let data2 = this.state.data;
		data2.max_length = event.target.value;
		this.setState( { data: data2 } );
	}

	onChangeCssClass(event) {
		let data2 = this.state.data;
		data2.css_class = event.target.value;
		this.setState( { data: data2 } );
	}

	onChangePlaceholder(event) {
		let data2 = this.state.data;
		data2.placeholder = event.target.value;
		this.setState( { data: data2 } );
	}

	onChangeValue(event) {
		let data2 = this.state.data;
		data2.value = event.target.value;
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

	onChangeDateFormat(event) {
		let data2 = this.state.data;
		data2.date_format = event.target.value;
		this.setState( { data: data2 } );
	}

	onChangeDaysBefore(event) {
		let data2 = this.state.data;
		data2.days_before = event.target.value;
		this.setState( { data: data2 } );
	}

	onChangeDaysAfter(event) {
		let data2 = this.state.data;
		data2.days_after = event.target.value;
		this.setState( { data: data2 } );
	}

	onChangeLogic(event) {
		let data2 = this.state.data;
		data2.logic = !data2.logic;
		this.setState( { data: data2 } );
	}

	onChangeLogicOperator(event) {
		let data2 = this.state.data;
		data2.logic_operator = event.target.value;
		this.setState( { data: data2 } );
	}

	onClickToggleDisplay(event) {
		let data2 = this.state.data;
		data2.display = !data2.display;
		this.setState( { data: data2 } );
	}

	handleMouseEnter(event) {
		this.setState( { mouseEnter: true } );
	}

	handleMouseLeave(event) {
		this.setState( { mouseEnter: false } );
	}

	handleAddOption(event) {

		event.preventDefault();

		let data = this.state.data;
		if ( typeof data.options == 'undefined' ) {
			data.options = [];
		}

		var key = "fpf_"+Math.floor((Math.random() * 10000000) + 1);
		var option = { id: key, value: '', label: '', price_type: fpf_price_type_options[0].value, price: '' };

		data.options.push( option );

		this.setState(
			{ data: data }
		);
	}

	onSortEndOptions = ( {oldIndex, newIndex} ) => {
		let data = this.state.data;
		data.options = arrayMove( data.options, oldIndex, newIndex );
		this.setState({
			data: data
		});

	};

	onRemoveOption( i ) {
		let data = this.state.data;
		for ( var n = 0 ; n < data.options.length ; n++) {
			if ( data.options[n].id == i ) {
				var removedObject = data.options.splice(n,1);
				removedObject = null;
				break;
			}
		}
		this.setState({data: data});
	}


	handleAddRule(event) {

		event.preventDefault();

		let data = this.state.data;
		if ( typeof data.logic_rules == 'undefined' ) {
			data.logic_rules = [];
		}

		var key = "fpf_"+Math.floor((Math.random() * 10000000) + 1);
		var rule = { id: key, field: '', compare: '', field_value: '' };

		data.logic_rules.push( rule );

		this.setState(
			{ data: data }
		);
	}

	onSortEndRules = ( {oldIndex, newIndex} ) => {
		let data = this.state.data;
		data.logic_rules = arrayMove( data.logic_rules, oldIndex, newIndex );
		this.setState({
			data: data
		});

	};

	onRemoveRule( i ) {
		let data = this.state.data;
		for ( var n = 0 ; n < data.logic_rules.length ; n++) {
			if ( data.logic_rules[n].id == i ) {
				var removedObject = data.logic_rules.splice(n,1);
				removedObject = null;
				break;
			}
		}
		this.setState({data: data});
	}


	render() {
		const showHide = {
			'display': this.state.data.display ? 'block' : 'none'
		};
		const required = this.state.data.required ? '*' : '';
		const toggleClass = this.state.data.display ? "open" : "closed";

		return (
			<div className={"fpf-field-object " + toggleClass}>
				<div className="fpf-field-title-row" onClick={this.onClickToggleDisplay} onMouseEnter={this.handleMouseEnter} onMouseLeave={this.handleMouseLeave}>
					<div className="fpf-field-sort">
						<DragHandle/>
					</div>
					<div className="fpf-field-title">
						<strong>{this.state.data.title} {required}</strong>
						{ this.state.mouseEnter ?
							<span className="fpf-row-actions">
                                <span className="fpf-edit-action">{fpf_admin.edit_label}</span>
								&nbsp;|&nbsp;
								<span className="fpf-delete-action" onClick={() => this.props.onRemove(this.state.data.id)}>{fpf_admin.delete_label}{this.state.index}</span>
                            </span>
							:
							<span className="fpf-row-actions">
                                &nbsp;
                            </span>
						}
					</div>
					<div className="fpf-field-type">{fpf_field_types[this.state.data.type]['label']}</div>
				</div>
				<div className="fpf-field-inputs" style={showHide}>
					<table className="fpf-table">
						<tbody>
						<tr className="fpf-field">
							<td className="fpf-label">
								<label htmlFor={"field_title_" + this.state.id}>{fpf_admin.field_title_label}</label>
							</td>

							<td className="fpf-input">
								<input type="text" className="fpf-field-title" id={"field_title_" + this.state.id} name="field_title" value={this.state.data.title} onChange={this.onChangeTitle} required={true}/>
							</td>
						</tr>

						<tr className="fpf-field">
							<td className="fpf-label">
								<label htmlFor={"field_type_" + this.state.id}>{fpf_admin.field_type_label}</label>
							</td>

							<td className="fpf-input">
								<select id={"field_type_" + this.state.id} name="field_type" onChange={this.onChangeType} value={this.state.data.type}>
									{
										fpf_field_type_options.map(function (item) {
											return <option key={item.value} value={item.value}>{item.label}</option>;
										})
									}
								</select>
								{  fpf_field_types[this.state.data.type]['is_available'] ?
									null
									:
									<span>
                                            <br/><br/>
										{fpf_admin.fields_adv} <a href={fpf_admin.fields_adv_link}>{fpf_admin.fields_adv_link_text}</a>
                                        </span>
								}
							</td>
						</tr>
						</tbody>
					</table>
					{  fpf_field_types[this.state.data.type]['is_available'] ?
						<table className="fpf-table fpf-table-field-properies">
							<tbody>
							{  fpf_field_types[this.state.data.type]['has_required'] ?
								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_required_" + this.state.id}>{fpf_admin.field_required_label}</label>
									</td>

									<td className="fpf-input">
										<input
											name="field_required"
											className="fpf-field-required"
											id={"field_required_" + this.state.id}
											type="checkbox"
											checked={this.state.data.required}
											onChange={this.onChangeRequired}
										/>
									</td>
								</tr>
								: null
							}

							{  fpf_field_types[this.state.data.type]['has_max_length'] ?
								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_max_length_" + this.state.id}>{fpf_admin.field_max_length_label}</label>
									</td>

									<td className="fpf-input">
										<input
											type="number"
											className="fpf-field-max-length"
											id={"field_max_length_" + this.state.id}
											name="field_max_length"
											value={this.state.data.max_length}
											onChange={this.onChangeMaxLength}
											step="1"
											min="1"
										/>
									</td>
								</tr>
								: null
							}

							{  fpf_field_types[this.state.data.type]['has_placeholder'] ?
								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_placeholder_" + this.state.id}>{fpf_admin.field_placeholder_label}</label>
									</td>

									<td className="fpf-input">
										<input
											type="text"
											className="fpf-field-placeholder"
											id={"field_placeholder_" + this.state.id}
											name="field_placeholder"
											value={this.state.data.placeholder}
											onChange={this.onChangePlaceholder}
										/>
									</td>
								</tr>
								: null
							}

							{  fpf_field_types[this.state.data.type]['has_value'] ?
								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_value_" + this.state.id}>{fpf_admin.field_value_label}</label>
									</td>

									<td className="fpf-input">
										<input type="text" className="fpf-field-value"
											   id={"field_value_" + this.state.id} name="field_value"
											   value={this.state.data.value} onChange={this.onChangeValue}/>
									</td>
								</tr>
								: null
							}

							<tr className="fpf-field">
								<td className="fpf-label">
									<label
										htmlFor={"field_css_class_" + this.state.id}>{fpf_admin.field_css_class_label}</label>
								</td>

								<td className="fpf-input">
									<input type="text" className="fpf-field-css-class"
										   id={"field_css_class_" + this.state.id} name="field_css_class"
										   value={this.state.data.css_class} onChange={this.onChangeCssClass}/>
								</td>
							</tr>

							{  this.state.data.type == 'fpfdate' ?

								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_date_format_" + this.state.id}>{fpf_admin.field_date_format_label}
										</label>
									</td>
									<td className="fpf-input">
										<input
											type="text"
											className="fpf-field-date-format"
											id={"field_date_format_" + this.state.id}
											name="field_date_format"
											value={this.state.data.date_format}
											onChange={this.onChangeDateFormat}
										/>
									</td>
								</tr>

								:
								null
							}

							{  this.state.data.type == 'fpfdate' ?

								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_days_before_" + this.state.id}>{fpf_admin.field_days_before_label}
										</label>
									</td>
									<td className="fpf-input">
										<input
											type="number"
											className="fpf-field-days-before"
											id={"field_days_before_" + this.state.id}
											name="field_days_before"
											value={this.state.data.days_before}
											onChange={this.onChangeDaysBefore}
											step="1"
										/>
									</td>
								</tr>

								:
								null
							}

							{  this.state.data.type == 'fpfdate' ?

								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_days_after_" + this.state.id}>{fpf_admin.field_days_after_label}
										</label>
									</td>
									<td className="fpf-input">
										<input
											type="number"
											className="fpf-field-days-after"
											id={"field_days_after_" + this.state.id}
											name="field_days_after"
											value={this.state.data.days_after}
											onChange={this.onChangeDaysAfter}
											step="1"
										/>
									</td>
								</tr>

								:
								null
							}

							{  fpf_field_types[this.state.data.type]['has_price'] ?

								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_price_type_" + this.state.id}>{fpf_admin.field_price_type_label}</label>
									</td>

									<td className="fpf-input">
										<select name="price_type" onChange={this.onChangePriceType}
												value={this.state.data.price_type}>
											{
												fpf_price_type_options.map(function (item) {
													return <option key={item.value}
																   value={item.value}>{item.label}</option>;
												})
											}
										</select>
									</td>
								</tr>

								:
								null
							}

							{  fpf_field_types[this.state.data.type]['price_not_available'] ?

								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_price_type_" + this.state.id}>{fpf_admin.field_price_type_label}</label>
									</td>

									<td className="fpf-input">
										{fpf_admin.price_adv} <a href={fpf_admin.price_adv_link}>{fpf_admin.price_adv_link_text}</a>
									</td>
								</tr>

								:
								null
							}

							{  fpf_field_types[this.state.data.type]['has_price'] ?

								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_price_" + this.state.id}>{fpf_admin.field_price_label}</label>
									</td>

									<td className="fpf-input">
										<input
											type="number"
											className="fpf-field-price"
											id={"field_price_" + this.state.id}
											name="field_price"
											value={this.state.data.price}
											onChange={this.onChangePrice}
											step="0.01"
										/>
									</td>
								</tr>

								:
								null
							}

							{  fpf_field_types[this.state.data.type]['price_not_available'] ?

								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_price_" + this.state.id}>{fpf_admin.field_price_label}</label>
									</td>

									<td className="fpf-input">
										{fpf_admin.price_adv} <a href={fpf_admin.price_adv_link}>{fpf_admin.price_adv_link_text}</a>
									</td>
								</tr>

								:
								null
							}

							{ fpf_field_types[this.state.data.type]['has_options'] ?
								<tr className="fpf-field">
									<td className="fpf-label">
										<label>{fpf_admin.field_options_label}</label>
									</td>
									<td className="fpf-input">
										<table className="fpf-table fpf-options">
											<thead>
											<tr>
												<th className="fpf-row-handle"></th>
												<th>{fpf_admin.option_value_label}</th>
												<th>{fpf_admin.option_label_label}</th>
												{ fpf_field_types[this.state.data.type]['has_price_in_options'] ?
													<th className="fpf-row-price">{fpf_admin.option_price_type_label}</th>
													:
													null
												}
												{ fpf_field_types[this.state.data.type]['has_price_in_options'] ?
													<th>{fpf_admin.option_price_label}</th>
													:
													null
												}
												{ fpf_field_types[this.state.data.type]['price_not_available_in_options'] ?
													<th>{fpf_admin.option_price_label}</th>
													:
													null
												}
												<th className="fpf-row-handle"></th>
											</tr>
											</thead>

											{fpf_field_types[this.state.data.type]['has_price_in_options'] ?
												<SortableListOptions
													items={this.state.data.options}
													onRemove={this.onRemoveOption}
													useDragHandle={true}
													onSortEnd={this.onSortEndOptions}
													helperClass="fpf-option-drag"
												/>
												:
												null
											}
											{ fpf_field_types[this.state.data.type]['price_not_available_in_options'] ?
												<SortableListOptionsNoPrice
													items={this.state.data.options}
													onRemove={this.onRemoveOption}
													useDragHandle={true}
													onSortEnd={this.onSortEndOptions}
													helperClass="fpf-option-drag"
												/>
												:
												null
											}

										</table>
										<button className="fpf-add-option button button-primary"
												onClick={this.handleAddOption}>
											{fpf_admin.add_option_label}
										</button>
									</td>
								</tr>
								:
								null
							}

							{  fpf_field_types[this.state.data.type]['has_logic'] ?
								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_logic_" + this.state.id}
										>
											{fpf_admin.field_logic_label}
										</label>
									</td>
									<td className="fpf-input">
										<input
											name="field_logic"
											className="fpf-field-logic"
											id={"field_logic_" + this.state.id}
											type="checkbox"
											checked={this.state.data.logic}
											onChange={this.onChangeLogic}
										/>
									</td>
								</tr>
								:
								null
							}

							{  fpf_field_types[this.state.data.type]['logic_not_available'] ?

								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_logic_" + this.state.id}
										>
											{fpf_admin.field_logic_label}
										</label>
									</td>

									<td className="fpf-input">
										{fpf_admin.logic_adv} <a href={fpf_admin.logic_adv_link}>{fpf_admin.logic_adv_link_text}</a>
									</td>
								</tr>

								:
								null
							}

							{  fpf_field_types[this.state.data.type]['has_logic'] && this.state.data.logic ?
								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_logic_operator" + this.state.id}
										>
											{fpf_admin.logic_label_operator}
										</label>
									</td>
									<td className="fpf-input">
										<select name="field_logic_operator" onChange={this.onChangeLogicOperator} value={this.state.data.logic_operator}>
											<option value="or">{fpf_admin.logic_label_operator_or}</option>
											<option value="and">{fpf_admin.logic_label_operator_and}</option>
										</select>
									</td>
								</tr>
								:
								null
							}

							{  fpf_field_types[this.state.data.type]['has_logic'] && this.state.data.logic ?
								<tr className="fpf-field">
									<td className="fpf-label">
										<label
											htmlFor={"field_logic_rules" + this.state.id}
										>
											{fpf_admin.logic_label_rules}
										</label>
									</td>
									<td className="fpf-input">
										<table className="fpf-table fpf-options">
											<thead>
											<tr>
												<th className="fpf-row-handle"></th>
												<th>{fpf_admin.logic_label_field}</th>
												<th>{fpf_admin.logic_label_compare}</th>
												<th>{fpf_admin.logic_label_value}</th>
												<th className="fpf-row-handle"></th>
											</tr>
											</thead>

											<SortableListRules
												items={this.state.data.logic_rules}
												onRemove={this.onRemoveRule}
												useDragHandle={true}
												onSortEnd={this.onSortEndRules}
												helperClass="fpf-option-drag"
											/>

										</table>
										<button className="fpf-add-option button button-primary"
												onClick={this.handleAddRule}>
											{fpf_admin.add_rule_label}
										</button>
									</td>
								</tr>
								:
								null
							}

							</tbody>
						</table>
						: null
					}
				</div>
			</div>
		);
	}
}
