import React, { Component } from "react";
import Table from "./common/Table";

class UserTable extends Component {
  columns = [{ path: "name", label: "Name" }];

  render() {
    const { columns, data, onSort, sortColumn, pageNumber, pageSize } =
      this.props;

    return (
      <Table
        id="siteRid"
        columns={columns}
        data={data}
        onSort={onSort}
        sortColumn={sortColumn}
        showSerial={true}
        pageNumber={pageNumber}
        pageSize={pageSize}
      />
    );
  }
}

export default UserTable;
