import {SortableHandle} from "react-sortable-hoc";
import React from "react";

export const DragHandle = SortableHandle(() => <span className="fpf-drag"><span className="dashicons dashicons-menu"></span></span>);