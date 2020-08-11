import React, {Component} from 'react';
import Select from 'react-select';

const getProductsOptions = (input) => {
	return fetch( fpf_admin.rest_url + 'flexible_product_fields/v1/products/' + fpf_admin.rest_param + 'search=' + input + '&_wp_rest_nonce=' + fpf_admin.rest_nonce,
	).then((response) => {
		return response.json();
	}).then((json) => {
		return { options: json };
	});
}

let getCategoriesOptions = (input) => {
	return fetch( fpf_admin.rest_url + 'flexible_product_fields/v1/categories/' + fpf_admin.rest_param + 'search=' + input + '&_wp_rest_nonce=' + fpf_admin.rest_nonce,
	).then((response) => {
		return response.json();
	}).then((json) => {
		return { options: json };
	});
}

export default class FPF_Settings_Field extends React.Component {

	constructor(props) {
		super( props );
		this.state = {
			assign_to: props.assign_to,
			section: props.section,
			products: props.products,
			categories: props.categories,
			menu_order: props.menu_order,
		}
		if ( fpf_assign_to_values[this.state.assign_to.value]['is_available'] ) {
			document.getElementById('fpf_fields').style.display = 'block';
		}
		else {
			document.getElementById('fpf_fields').style.display = 'none';
		}
		this.assignToChange = this.assignToChange.bind(this);
		this.sectionChange = this.sectionChange.bind(this);
		this.productsChange = this.productsChange.bind(this);
		this.categoriesChange = this.categoriesChange.bind(this);
		this.menuOrderChange = this.menuOrderChange.bind(this);

	}

	assignToChange(event) {
		var assign_to = this.state.assign_to;
		assign_to.value = event.target.value;
		this.setState( { assign_to: assign_to } );
		fpf_settings.assign_to = assign_to;
		if ( fpf_assign_to_values[this.state.assign_to.value]['is_available'] ) {
			document.getElementById('fpf_fields').style.display = 'block';
		}
		else {
			document.getElementById('fpf_fields').style.display = 'none';
		}
	}

	assignToChange2(val) {
		var assign_to = this.state.assign_to;
		assign_to.value = val.value;
		this.setState( { assign_to: assign_to } );
	}

	sectionChange(event) {
		var section = this.state.section;
		section.value = event.target.value;
		this.setState( { section: section } );
	}

	menuOrderChange(event) {
		var menu_order = this.state.menu_order;
		menu_order.value = event.target.value;
		this.setState( { menu_order: menu_order } );
	}

	sectionChange2(val) {
		var section = this.state.section;
		section.value = val.value;
		this.setState( { section: section } );
	}

	productsChange(val) {
		var products2 = this.state.products;
		products2.value = val;
		this.setState( { products: products2 } );
	}

	categoriesChange(val) {
		var categories2 = this.state.categories;
		categories2.value = val;
		this.setState( { categories: categories2 } );
	}

	render() {
		return (
			<table className="fpf-table">
				<tbody>
				<tr className="fpf-field fpf-field-section">
					<td className="fpf-label">
						<label htmlFor="">{fpf_admin.section_label}</label>
					</td>

					<td className="fpf-input">
						<select name="section" onChange={this.sectionChange} value={this.state.section.value}>
							{
								fpf_sections_options.map(function (item) {
									return <option key={item.value} value={item.value}>{item.label}</option>;
								})
							}
						</select>
					</td>
				</tr>
				<tr className="fpf-field fpf-field-assign-to-label">
					<td className="fpf-label">
						<label htmlFor="">{fpf_admin.assign_to_label}</label>
					</td>

					<td className="fpf-input">
						<select name="assing_to" onChange={this.assignToChange} value={this.state.assign_to.value}>
							{
								fpf_assign_to_options.map(function (item) {
									return <option key={item.value}  disabled={item.disabled} value={item.value}>{item.label}</option>;
								})
							}
						</select>
						{  fpf_assign_to_values[this.state.assign_to.value]['is_available'] ?
							null
							:
							<span>
                                <br/><br/>
								{fpf_admin.assign_to_adv} <a href={fpf_admin.assign_to_adv_link}>{fpf_admin.assign_to_adv_link_text}</a>
							</span>
						}
					</td>
				</tr>
				{ this.state.assign_to.value === 'product' && fpf_assign_to_values[this.state.assign_to.value]['is_available'] ?
					<tr className="fpf-field fpf-field-product-label">
						<td className="fpf-label">
							<label htmlFor="">{fpf_admin.products_label}</label>
						</td>

						<td className="fpf-input">
							{  fpf_assign_to_values[this.state.assign_to.value]['is_available'] ?
								<Select.Async
									name="products"
									value={this.state.products.value}
									onChange={this.productsChange}
									loadOptions={getProductsOptions}
									placeholder={fpf_admin.select_placeholder}
									searchPromptText={fpf_admin.select_type_to_search}
									multi={true}
									autoload={false}
									ref="products"
								/>
								:
								<span>
                                    {fpf_admin.assign_to_adv} <a href={fpf_admin.assign_to_adv_link}>{fpf_admin.assign_to_adv_link_text}</a>
                                </span>
							}
						</td>
					</tr>
					: null
				}
				{ this.state.assign_to.value === 'category' && fpf_assign_to_values[this.state.assign_to.value]['is_available'] ?
					<tr className="fpf-field fpf-field-categories">
						<td className="fpf-label">
							<label htmlFor="">{fpf_admin.categories_label}</label>
						</td>

						<td className="fpf-input">
							{  fpf_assign_to_values[this.state.assign_to.value]['is_available'] ?
								<Select.Async
									name="categories"
									value={this.state.categories.value}
									onChange={this.categoriesChange}
									loadOptions={getCategoriesOptions}
									searchPromptText={fpf_admin.select_type_to_search}
									placeholder={fpf_admin.select_placeholder}
									autoload={true}
									multi={true}
									ref="categories"
								/>
								:
								<span>
                                        {fpf_admin.assign_to_adv} <a href={fpf_admin.assign_to_adv_link}>{fpf_admin.assign_to_adv_link_text}</a>
                                </span>
							}
						</td>
					</tr>
					: null
				}
				<tr className="fpf-field fpf-field-menu-order-label">
					<td className="fpf-label">
						<label htmlFor="">{fpf_admin.menu_order_label}</label>
					</td>
					<td className="fpf-input">
						{ fpf_field_group_menu_order_is_available ?
							<input type="number" className="fpf-field-menu-order" name="menu_order" value={this.state.menu_order.value} onChange={this.menuOrderChange} step="1" />
							: <span>{fpf_admin.menu_order_adv} <a href={fpf_admin.menu_order_adv_link}>{fpf_admin.menu_order_adv_link_text}</a></span>
						}
					</td>
				</tr>
				</tbody>
			</table>
		);
	}
}
