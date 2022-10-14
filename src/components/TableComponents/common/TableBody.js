import React, { Component } from "react";
import _ from "lodash";
// import { hasValue } from "../../utils/helper";

class TableBody extends Component {
  renderCell = (item, column) => {
    if (column.content) return column.content(item);

    return _.get(item, column.path);
  };

  createKey = (id, column) => {
    return id + (column.key || column.path);
  };

  render() {
    const { id, data, columns, showSerial, pageNumber, pageSize } = this.props;

    const page = hasValue(pageNumber) ? pageNumber : 1;
    const sizeOfPage = hasValue(pageSize) ? pageSize : data.length;

    return (
      <tbody>
        {data.map((item, index) => (
          <tr className={item.className} key={item[id]}>
            {showSerial && <td>{index + 1 + (page - 1) * sizeOfPage}</td>}
            {columns.map((column) => (
              <td key={this.createKey(item[id], column)}>
                {this.renderCell(item, column)}
              </td>
            ))}
          </tr>
        ))}
      </tbody>
    );
  }
}

export default TableBody;
