import React from "react";
import {SortableComponent} from "./sortable.jsx";

export default class FPF_Fields extends React.Component {

	constructor( props ) {
		super( props );
		this.state = {
			fields: props.fields
		}
	}

	render() {
		return (
			<div>
				<SortableComponent items={this.state.fields} />
			</div>
		);
	}
}
