import axios from "axios";
import { useEffect, useState } from "react";
import UserTable from "./TableComponents/UserTable";

const columns = [
  { path: "cutomerId", label: "Customer ID" },
  { path: "name", label: "Name" },
];

export default function ListUser() {
  const [users, setUsers] = useState([]);
  useEffect(() => {
    getUsers();
  }, []);

  function getUsers() {
    axios.get("http://192.168.6.189/api/customers").then(function (response) {
      console.log(response.data);
      setUsers(response.data);
    });
  }

  const deleteUser = (id) => {
    axios
      .delete(`http://localhost:8888/api/user/${id}/delete`)
      .then(function (response) {
        console.log(response.data);
        getUsers();
      });
  };
  return (
    <div className="container-fluid">
      <h1>List Users</h1>
      <UserTable
        columns={columns}
        data={sites}
        pageNumber={currentPage}
        pageSize={pageSize}
      />

      {/* <table className="table table-hover">
        <thead>
          <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Address</th>
            <th>L1 Email</th>
            <th>L2 Email</th>
            <th>L3 Email</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{users.response && users.response.data[0].customerId}</td>
            <td>{users.response && users.response.data[0].name}</td>
            <td>{users.response && users.response.data[0].mobileNo}</td>
            <td>{users.response && users.response.data[0].address}</td>
            <td>{users.response && users.response.data[0].l1Email}</td>
            <td>{users.response && users.response.data[0].l2Email}</td>
            <td>{users.response && users.response.data[0].l3Email}</td>
          </tr>
        </tbody>
      </table> */}
    </div>
  );
}
