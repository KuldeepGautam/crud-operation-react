import React from "react";
import TableHeader from "./TableHeader";
import TableBody from "./TableBody";

const Table = ({
  columns,
  id,
  data,
  onSort,
  sortColumn,
  showSerial,
  pageNumber,
  pageSize,
}) => {
  return (
    <table className="table table-sm table-bordered table-hover table-responsive-sm">
      <TableHeader
        columns={columns}
        onSort={onSort}
        sortColumn={sortColumn}
        showSerial={showSerial}
      />
      <TableBody
        id={id}
        columns={columns}
        data={data}
        showSerial={showSerial}
        pageNumber={pageNumber}
        pageSize={pageSize}
      />
    </table>
  );
};

export default Table;
