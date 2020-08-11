import React from "react";
import FPF_Field from "./fpf_field.jsx";
import FPF_Rule from "./fpf_rule.jsx";
import FPF_Option_No_Price from "./fpf_option_no_price.jsx";
import FPF_Option from "./fpf_option.jsx";
import {SortableContainer, SortableElement, arrayMove} from 'react-sortable-hoc';

export const SortableItem = SortableElement (
	( {value, i, onRemove} ) =>
		<FPF_Field
			i={i}
			data={value}
			onRemove={onRemove}
		/>
);

export const SortableList = SortableContainer(({items,onRemove}) => {
	return (
		<div>
			{items.map((value, index) =>
				<SortableItem
					key={`item-${value.id}`}
					index={index}
					i={index}
					value={value}
					onRemove={onRemove}
				/>
			)}
		</div>
	);
});

export class SortableComponent extends React.Component {

	constructor( props ) {
		super( props );
		this.state = {
			items: props.items
		};
		this.onRemove = this.onRemove.bind(this);
	}

	onRemove( i ) {
		let items = this.state.items;
		for ( var n = 0 ; n < items.length ; n++) {
			if ( items[n].id == i ) {
				var removedObject = items.splice(n,1);
				removedObject = null;
				break;
			}
		}
		this.setState({items: items});
		Fields.setState( { fields: this.state.items } );
	}

	onSortEnd = ( {oldIndex, newIndex} ) => {
		this.setState({
			items: arrayMove( this.state.items, oldIndex, newIndex )
		});
		Fields.setState( { fields: this.state.items } );
		fpf_settings.fields = this.state.items;
	};

	render() {
		return (
			<SortableList
				items={this.state.items}
				onSortEnd={this.onSortEnd}
				useDragHandle={true}
				onRemove={this.onRemove}
			/>
		)
	}
}

export const SortableListOptionsNoPrice = SortableContainer(({items,onRemove}) => {
	return (
		<tbody>
		{items.map((value, index) =>
			<SortableItemOptionsNoPrice
				key={`item-${value.id}`}
				index={index}
				i={index}
				value={value}
				onRemove={onRemove}
			/>
		)}
		</tbody>
	);
});

export const SortableListOptions = SortableContainer(({items,onRemove}) => {
	return (
		<tbody>
		{items.map((value, index) =>
			<SortableItemOptions
				key={`item-${value.id}`}
				index={index}
				i={index}
				value={value}
				onRemove={onRemove}
			/>
		)}
		</tbody>
	);
});

export const SortableListRules = SortableContainer(({items,onRemove}) => {
	return (
		<tbody>
		{items.map((value, index) =>
			<SortableItemRules
				key={`item-${value.id}`}
				index={index}
				i={index}
				value={value}
				onRemove={onRemove}
			/>
		)}
		</tbody>
	);
});

export const SortableItemOptionsNoPrice = SortableElement(
	( {value, i, onRemove} ) =>
		<FPF_Option_No_Price
			value={value}
			i={i}
			onRemove={onRemove}
		/>
);

export const SortableItemOptions = SortableElement (
	( {value, i, onRemove} ) =>
		<FPF_Option
			value={value}
			i={i}
			onRemove={onRemove}
		/>
);

export const SortableItemRules = SortableElement (
	( {value, i, onRemove} ) =>
		<FPF_Rule
			value={value}
			i={i}
			onRemove={onRemove}
		/>
);
