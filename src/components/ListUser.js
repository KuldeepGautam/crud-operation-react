import axios from "axios";
import { useEffect, useState } from "react";

export default function ListUser() {
  const [state, setState] = useState([]);

  useEffect(() => {
    getUsers();
  }, []);

  function getUsers() {
    axios.get("http://192.168.0.182/api/customers").then(function (response) {
      console.log(response.data);
      setState(response.data.response.data);
    });
  }

  const deleteUser = (id) => {
    axios
      .delete(`http://localhost:8888/api/user/${id}/delete`)
      .then(function (response) {
        console.log(response.data);
        setState();
      });
  };

  return (
    <div className="container-fluid">
      <h1>List Users</h1>

      <table width="100%" className="table table-hover">
        <thead>
          <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Mobile</th>
            <th>Email</th>
            <th>Address</th>
            <th>Operations</th>
          </tr>
        </thead>
        <tbody>
          {state.map((user, key) => (
            <tr key={key}>
              <td>{user.customerId}</td>
              <td>{user.name}</td>
              <td>{user.mobileNo}</td>
              <td>{user.email}</td>
              <td>{user.address}</td>
              <td>
                <button className="m-1">Delete</button>
                <button>Edit</button>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
